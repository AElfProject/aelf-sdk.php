<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Script\Interpreter;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Exceptions\ScriptRuntimeException;
use BitWasp\Bitcoin\Exceptions\SignatureNotCanonical;
use BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use BitWasp\Bitcoin\Script\Opcodes;
use BitWasp\Bitcoin\Script\Script;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Script\ScriptInterface;
use BitWasp\Bitcoin\Script\ScriptWitness;
use BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use BitWasp\Bitcoin\Script\WitnessProgram;
use BitWasp\Bitcoin\Signature\TransactionSignature;
use BitWasp\Bitcoin\Transaction\SignatureHash\SigHash;
use BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class Interpreter implements InterpreterInterface
{

    /**
     * @var \BitWasp\Bitcoin\Math\Math
     */
    private $math;

    /**
     * @var BufferInterface
     */
    private $vchFalse;

    /**
     * @var BufferInterface
     */
    private $vchTrue;

    /**
     * @var array
     */
    private $disabledOps = [
        Opcodes::OP_CAT,    Opcodes::OP_SUBSTR, Opcodes::OP_LEFT,  Opcodes::OP_RIGHT,
        Opcodes::OP_INVERT, Opcodes::OP_AND,    Opcodes::OP_OR,    Opcodes::OP_XOR,
        Opcodes::OP_2MUL,   Opcodes::OP_2DIV,   Opcodes::OP_MUL,   Opcodes::OP_DIV,
        Opcodes::OP_MOD,    Opcodes::OP_LSHIFT, Opcodes::OP_RSHIFT
    ];

    /**
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(EcAdapterInterface $ecAdapter = null)
    {
        $ecAdapter = $ecAdapter ?: Bitcoin::getEcAdapter();
        $this->math = $ecAdapter->getMath();
        $this->vchFalse = new Buffer("", 0);
        $this->vchTrue = new Buffer("\x01", 1);
    }

    /**
     * Cast the value to a boolean
     *
     * @param BufferInterface $value
     * @return bool
     */
    public function castToBool(BufferInterface $value): bool
    {
        $val = $value->getBinary();
        for ($i = 0, $size = strlen($val); $i < $size; $i++) {
            $chr = ord($val[$i]);
            if ($chr !== 0) {
                if (($i === ($size - 1)) && $chr === 0x80) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @param BufferInterface $signature
     * @return bool
     */
    public function isValidSignatureEncoding(BufferInterface $signature): bool
    {
        try {
            TransactionSignature::isDERSignature($signature);
            return true;
        } catch (SignatureNotCanonical $e) {
            /* In any case, we will return false outside this block */
        }

        return false;
    }

    /**
     * @param int $opCode
     * @param BufferInterface $pushData
     * @return bool
     * @throws \Exception
     */
    public function checkMinimalPush($opCode, BufferInterface $pushData): bool
    {
        $pushSize = $pushData->getSize();
        $binary = $pushData->getBinary();

        if ($pushSize === 0) {
            return $opCode === Opcodes::OP_0;
        } elseif ($pushSize === 1) {
            $first = ord($binary[0]);

            if ($first >= 1 && $first <= 16) {
                return $opCode === (Opcodes::OP_1 + ($first - 1));
            } elseif ($first === 0x81) {
                return $opCode === Opcodes::OP_1NEGATE;
            }
        } elseif ($pushSize <= 75) {
            return $opCode === $pushSize;
        } elseif ($pushSize <= 255) {
            return $opCode === Opcodes::OP_PUSHDATA1;
        } elseif ($pushSize <= 65535) {
            return $opCode === Opcodes::OP_PUSHDATA2;
        }

        return true;
    }

    /**
     * @param int $count
     * @return $this
     */
    private function checkOpcodeCount(int $count)
    {
        if ($count > 201) {
            throw new \RuntimeException('Error: Script op code count');
        }

        return $this;
    }

    /**
     * @param WitnessProgram $witnessProgram
     * @param ScriptWitnessInterface $scriptWitness
     * @param int $flags
     * @param CheckerBase $checker
     * @return bool
     */
    private function verifyWitnessProgram(WitnessProgram $witnessProgram, ScriptWitnessInterface $scriptWitness, int $flags, CheckerBase $checker): bool
    {
        $witnessCount = count($scriptWitness);

        if ($witnessProgram->getVersion() === 0) {
            $buffer = $witnessProgram->getProgram();
            if ($buffer->getSize() === 32) {
                // Version 0 segregated witness program: SHA256(Script) in program, Script + inputs in witness
                if ($witnessCount === 0) {
                    // Must contain script at least
                    return false;
                }

                $scriptPubKey = new Script($scriptWitness[$witnessCount - 1]);
                $stackValues = $scriptWitness->slice(0, -1);
                if (!$buffer->equals($scriptPubKey->getWitnessScriptHash())) {
                    return false;
                }
            } elseif ($buffer->getSize() === 20) {
                // Version 0 special case for pay-to-pubkeyhash
                if ($witnessCount !== 2) {
                    // 2 items in witness - <signature> <pubkey>
                    return false;
                }

                $scriptPubKey = ScriptFactory::scriptPubKey()->payToPubKeyHash($buffer);
                $stackValues = $scriptWitness;
            } else {
                return false;
            }
        } elseif ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_WITNESS_PROGRAM) {
            return false;
        } else {
            // Unknown versions are always 'valid' to permit future soft forks
            return true;
        }

        $mainStack = new Stack();
        foreach ($stackValues as $value) {
            $mainStack->push($value);
        }

        if (!$this->evaluate($scriptPubKey, $mainStack, SigHash::V1, $flags, $checker)) {
            return false;
        }

        if ($mainStack->count() !== 1) {
            return false;
        }

        if (!$this->castToBool($mainStack->bottom())) {
            return false;
        }

        return true;
    }

    /**
     * @param ScriptInterface $scriptSig
     * @param ScriptInterface $scriptPubKey
     * @param int $flags
     * @param CheckerBase $checker
     * @param ScriptWitnessInterface|null $witness
     * @return bool
     */
    public function verify(ScriptInterface $scriptSig, ScriptInterface $scriptPubKey, int $flags, CheckerBase $checker, ScriptWitnessInterface $witness = null): bool
    {
        static $emptyWitness = null;
        if ($emptyWitness === null) {
            $emptyWitness = new ScriptWitness();
        }

        $witness = is_null($witness) ? $emptyWitness : $witness;

        if (($flags & self::VERIFY_SIGPUSHONLY) !== 0 && !$scriptSig->isPushOnly()) {
            return false;
        }

        $stack = new Stack();
        if (!$this->evaluate($scriptSig, $stack, SigHash::V0, $flags, $checker)) {
            return false;
        }

        $backup = [];
        if ($flags & self::VERIFY_P2SH) {
            foreach ($stack as $s) {
                $backup[] = $s;
            }
        }

        if (!$this->evaluate($scriptPubKey, $stack, SigHash::V0, $flags, $checker)) {
            return false;
        }

        if ($stack->isEmpty()) {
            return false;
        }

        if (false === $this->castToBool($stack[-1])) {
            return false;
        }

        $program = null;
        if ($flags & self::VERIFY_WITNESS) {
            if ($scriptPubKey->isWitness($program)) {
                /** @var WitnessProgram $program */
                if ($scriptSig->getBuffer()->getSize() !== 0) {
                    return false;
                }

                if (!$this->verifyWitnessProgram($program, $witness, $flags, $checker)) {
                    return false;
                }

                $stack->resize(1);
            }
        }

        if ($flags & self::VERIFY_P2SH && (new OutputClassifier())->isPayToScriptHash($scriptPubKey)) {
            if (!$scriptSig->isPushOnly()) {
                return false;
            }

            $stack = new Stack();
            foreach ($backup as $i) {
                $stack->push($i);
            }

            // Restore mainStack to how it was after evaluating scriptSig
            if ($stack->isEmpty()) {
                return false;
            }

            // Load redeemscript as the scriptPubKey
            $scriptPubKey = new Script($stack->bottom());
            $stack->pop();

            if (!$this->evaluate($scriptPubKey, $stack, 0, $flags, $checker)) {
                return false;
            }

            if ($stack->isEmpty()) {
                return false;
            }

            if (!$this->castToBool($stack->bottom())) {
                return false;
            }

            if ($flags & self::VERIFY_WITNESS) {
                if ($scriptPubKey->isWitness($program)) {
                    /** @var WitnessProgram $program */
                    if (!$scriptSig->equals(ScriptFactory::sequence([$scriptPubKey->getBuffer()]))) {
                        return false; // SCRIPT_ERR_WITNESS_MALLEATED_P2SH
                    }

                    if (!$this->verifyWitnessProgram($program, $witness, $flags, $checker)) {
                        return false;
                    }

                    $stack->resize(1);
                }
            }
        }

        if ($flags & self::VERIFY_CLEAN_STACK) {
            if (!($flags & self::VERIFY_P2SH !== 0) && ($flags & self::VERIFY_WITNESS !== 0)) {
                return false; // implied flags required
            }

            if (count($stack) !== 1) {
                return false; // Cleanstack
            }
        }

        if ($flags & self::VERIFY_WITNESS) {
            if (!$flags & self::VERIFY_P2SH) {
                return false; //
            }

            if ($program === null && !$witness->isNull()) {
                return false; // SCRIPT_ERR_WITNESS_UNEXPECTED
            }
        }

        return true;
    }

    /**
     * @param Stack $vfStack
     * @param bool $value
     * @return bool
     */
    public function checkExec(Stack $vfStack, bool $value): bool
    {
        $ret = 0;
        foreach ($vfStack as $item) {
            if ($item === $value) {
                $ret++;
            }
        }

        return (bool) $ret;
    }

    /**
     * @param ScriptInterface $script
     * @param Stack $mainStack
     * @param int $sigVersion
     * @param int $flags
     * @param CheckerBase $checker
     * @return bool
     */
    public function evaluate(ScriptInterface $script, Stack $mainStack, int $sigVersion, int $flags, CheckerBase $checker): bool
    {
        $hashStartPos = 0;
        $opCount = 0;
        $zero = gmp_init(0, 10);
        $altStack = new Stack();
        $vfStack = new Stack();
        $minimal = ($flags & self::VERIFY_MINIMALDATA) !== 0;
        $parser = $script->getScriptParser();

        if ($script->getBuffer()->getSize() > 10000) {
            return false;
        }

        try {
            foreach ($parser as $operation) {
                $opCode = $operation->getOp();
                $pushData = $operation->getData();
                $fExec = !$this->checkExec($vfStack, false);

                // If pushdata was written to
                if ($operation->isPush() && $operation->getDataSize() > InterpreterInterface::MAX_SCRIPT_ELEMENT_SIZE) {
                    throw new \RuntimeException('Error - push size');
                }

                // OP_RESERVED should not count towards opCount
                if ($opCode > Opcodes::OP_16 && ++$opCount) {
                    $this->checkOpcodeCount($opCount);
                }

                if (in_array($opCode, $this->disabledOps, true)) {
                    throw new \RuntimeException('Disabled Opcode');
                }

                if ($fExec && $operation->isPush()) {
                    // In range of a pushdata opcode
                    if ($minimal && !$this->checkMinimalPush($opCode, $pushData)) {
                        throw new ScriptRuntimeException(self::VERIFY_MINIMALDATA, 'Minimal pushdata required');
                    }

                    $mainStack->push($pushData);
                    // echo " - [pushed '" . $pushData->getHex() . "']\n";
                } elseif ($fExec || (Opcodes::OP_IF <= $opCode && $opCode <= Opcodes::OP_ENDIF)) {
                    // echo "OPCODE - " . $script->getOpcodes()->getOp($opCode) . "\n";
                    switch ($opCode) {
                        case Opcodes::OP_1NEGATE:
                        case Opcodes::OP_1:
                        case Opcodes::OP_2:
                        case Opcodes::OP_3:
                        case Opcodes::OP_4:
                        case Opcodes::OP_5:
                        case Opcodes::OP_6:
                        case Opcodes::OP_7:
                        case Opcodes::OP_8:
                        case Opcodes::OP_9:
                        case Opcodes::OP_10:
                        case Opcodes::OP_11:
                        case Opcodes::OP_12:
                        case Opcodes::OP_13:
                        case Opcodes::OP_14:
                        case Opcodes::OP_15:
                        case Opcodes::OP_16:
                            $num = \BitWasp\Bitcoin\Scr