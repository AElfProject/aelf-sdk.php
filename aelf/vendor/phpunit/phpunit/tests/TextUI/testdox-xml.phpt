<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Address;

use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Exceptions\UnrecognizedAddressException;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use BitWasp\Bitcoin\Script\P2shScript;
use BitWasp\Bitcoin\Script\ScriptInterface;
use BitWasp\Bitcoin\Script\ScriptType;
use BitWasp\Bitcoin\Script\WitnessProgram;
use BitWasp\Bitcoin\Script\WitnessScript;
use BitWasp\Bitcoin\SegwitBech32;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class AddressCreator extends BaseAddressCreator
{
    /**
     * @param string $strAddress
     * @param NetworkInterface $network
     * @return Base58Address|null
     */
    protected function readBase58Address(string $strAddress, NetworkInterface $network)
    {
        try {
            $data = Base58::decodeCheck($strAddress);
            $prefixByte = $data->slice(0, $network->getP2shPrefixLength())->getHex();

            if ($prefixByte === $network->getP2shByte()) {
                return new ScriptHashAddress($data->slice(1));
            } else if ($prefixByte === $network->getAddressByte()) {
                return new PayToPubKeyHashAddress($data->slice($network->getAddressPrefixLength()));
            }
        } catch (\Exception $e) {
            // Just return null
        }

        return null;
    }

    /**
     * @param string $strAddress
     * @param NetworkInterface $network
     * @return SegwitAddress|null
     */
    protected function readSegwitAddress(string $strAddress, NetworkInterface $network)
    {
        try {
            list ($version, $program) = \BitWasp\Bech32\decodeSegwit($network->getSegwitBech32Prefix(), $strAddress);

            if (0 === $version) {
                $wp = WitnessProgram::v0(new Buffer($program));
            } else {
                $wp = new WitnessProgram($version, new Buffer($program));
            }

            return new SegwitAddress($wp);
        } catch (\Exception $e) {
            // Just return null
        }

        return null;
    }

    /**
     * @param ScriptInterface $outputScript
     * @return Address
     */
    public function fromOutputScript(ScriptInterface $outputScript): Address
    {
        if ($outputScript instanceof P2shScript || $outputScript instanceof WitnessScript) {
            throw new \RuntimeException("P2shScript & WitnessScript's are not accepted by fromOutputScript");
        }

        $wp = null;
        if ($outputScript->isWitness($wp)) {
            /** @var WitnessP