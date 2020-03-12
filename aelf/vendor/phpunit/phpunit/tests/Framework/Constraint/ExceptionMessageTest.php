[$i]->compressedAddr;

            $this->assertEquals($addr, $sxAddr, 'Something went wrong for privateKey : ' . $bitcoinECDSA->getPrivateKey() . ', please report us the issue');

            $this->assertTrue($bitcoinECDSA->validateAddress($addr), 'Something went wrong while validating address : ' . $addr . ' with private key : ' . $bitcoinECDSA->getPrivateKey() . ', please report us the issue');

            $this->assertTrue($bitcoinECDSA->validateWifKey($bitcoinECDSA->getWif()), 'Something went wrong while validating Wif key : ' . $bitcoinECDSA->getWif() . ' with private key : ' . $bitcoinECDSA->getPrivateKey() . ', please report us the issue');

            //test : uncompressed public key
            if(!$expectedRes)
                $ucSxPubKey = exec('echo -n "' . $privKey . '" | sx pubkey false');
            else
                $ucSxPubKey = $expectedRes[$i]->unCompressedPubKey;

            $bpUcPubKey = $bitcoinECDSA->getUncompressedPubKey();
            $this->assertEquals($bpUcPubKey, $ucSxPubKey, 'Something went wrong for privateKey : ' . $bitcoinECDSA->getPrivateKey() . ', please report us the issue');

            // test : uncompressed address
            if(!$expectedRes)
                $ucSxAddr = ex