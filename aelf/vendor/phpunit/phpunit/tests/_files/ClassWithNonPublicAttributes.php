checksig->isVerify()) {
                    $stack->push($value ? new Buffer("\x01") : new Buffer());
                }

                if (!$expectFalse) {
                    $checksig->setSignature(0, $this->txSigSerializer->parse($vchSig));
                }
            }

            $checksig->setKey(0, $this->parseStepPublicKey($checksig->getSolution()));
        } else if (ScriptType::MULTISIG === $checksig->getType()) {
            /** @var Multisig $info */
            $info = $checksig->getInfo();
            $keyBuffers = $info->getKeyBuffers();
            foreach ($keyBuffers as $idx => $keyBuf) {
                $checksig->setKey($idx, $this->parseStepPublicKey($keyBuf));
            }

            $value = false;
            if ($this->padUnsignedMultisigs) {
                // Multisig padding is only used for partially signed transactions,
                // never fully signed. It is recognized by a scriptSig with $keyCount+1
