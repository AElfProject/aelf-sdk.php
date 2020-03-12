<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Script;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use BitWasp\Bitcoin\Script\Interpreter\InterpreterInterface;
use BitWasp\Bitcoin\Script\Interpreter\Number;
use BitWasp\Bitcoin\Script\Parser\Parser;
use BitWasp\Bitcoin\Serializable;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class Script extends Serializable implements ScriptInterface
{

    /**
     * @var Opcodes
     */
    protected $opCodes;

    /**
     * @var string
     */
    protected $script;

    /**
     * @var BufferInterface|null
     */
    protected $scriptHash;

    /**
     * @var BufferInterface|null
     */
    protected $witnessScriptHash;

    /**
     * @param BufferInterface $script
     * @param Opcodes|null $opCodes
     */
    public function __construct(BufferInterface $script = null, Opcodes $opCodes = null)
    {
        $this->script = $script instanceof BufferInterface ? $script->getBinary() : '';
        $this->opCodes = $opCodes ?: new Opcodes();
    }

    /**
     * @return BufferInterface
     */
    public function getBuffer(): BufferInterface
    {
        return new Buffer($this->script);
    }

    /**
     * @return Parser
     */
    public function getScriptParser(): Parser
    {
        return new Parser(Bitcoin::getMath(), $this);
    }

    /**
     * Get all opcodes
     *
     * @return Opcodes
     */
    public function getOpCodes(): Opcodes
    {
        return $this->opCodes;
    }

    /**
     * Return a buffer containing the HASH160 of this script.
     *
     * @return BufferInterface
     */
    public function getScriptHash(): BufferInterface
    {
        if (null === $this->scriptHash) {
            $this->scriptHash = Hash::sha256ripe160($this->getBuffer());
        }

        return $this->scriptHash;
    }

    /**
     * Return a buffer containing the SHA256 of this script.
     *
     * @return BufferInterface
     */
    public function getWitnessScriptHash(): BufferInterface
    {
        if (null === $this->witnessScriptHash) {
            $this->witnessScriptHash = Hash::sha256($this->getBuffer());
        }

        return $this->witnessScriptHash;
    }

    /**
     * @param bool|true $accurate
     * @return int
     */
    public function countSigOps(bool $accurate = true): int
    {
        $count = 0;
        $parser = $this->getScriptParser();

        $lastOp = 0xff;
        try {
            foreach ($parser as $exec) {
                $op = $exec->getOp();

                // None of these are pushdatas, so just an opcode
                if ($op === Opcodes::OP_CHECKSIG || $op === Opcodes::OP_CHECKSIGVERIFY) {
                    $count++;
                } elseif ($op === Opcodes::OP_CHECKMULTISIG || $op === Opcodes::OP_CHECKMULTISIGVERIFY) {
                    if ($accurate && ($lastOp >= Opcodes::OP_1 && $lastOp <= Opcodes::OP_16)) {
                        $count += decodeOpN($lastOp);
                    } else {
                        $count += 20;
                    }
                }

                $lastOp = $op;
            }
        } catch (\Exception $e) {
            /* Script parsing failures don't count, and terminate the loop */
        }

        return $count;
    }

    /**
     * @param WitnessProgram $program
     * @param ScriptWitnessInterface $scriptWitness
     * @return int
     */
    private function witnessSigOps(WitnessProgram $program, ScriptWitnessInterface $scriptWitness): int
    {
        if ($program->getVersion() === 0) {
            $size = $program->getProgram()->getSize();
            if ($size === 32 && count($scriptWitness) > 0) {
                $script = new Script($scriptWitness->bottom());
                return $script->countSigOps(true);
            }

            if ($size === 20) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param ScriptInterface $scriptSig
     * @param ScriptWitnessInterface $scriptWitness
     * @param int $flags
     * @return int
     */
    public function countWitnessSigOps(ScriptInterface $scriptSig, ScriptWitnessInterface $scriptWitness, int $flags): int
    {
        if (($flags & InterpreterInterface::VERIFY_WITNESS) === 0) {
            return 0;
        }

        $program = null;
        if ($this->isWitness($program)) {
            /** @var WitnessProgram $program */
            return $this->witnessSigOps($program, $scriptWitness);
        }

        if ((new OutputClassifier())->isPa