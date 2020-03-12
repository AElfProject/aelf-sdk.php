blic function getAddressFromPrivateKey($privateKey) {
        $aelfkey = new BitcoinECDSA();
        $aelfkey->setPrivateKey($privateKey);
        $address = $aelfkey->getUncompressedAddress();
        return $address;
    }

    /**
     * Get the private sha256 signature.
     */
    public function getSignatureWithPrivateKey($privateKey,$txData){
        $secp256k1 = new Secp256k1();
     
        $signature = $secp256k1->sign($txData, $privateKey);
        // get r
        $r = $signature->getR();
        // get s
        $s = $signature->getS();
        // get recovery param
        $v = $signature->getRecoveryParam();
        // encode to hex
        $serializer = new HexSignatureSerializer();
        $signatureString = $serializer->serialize($signature);
        
        // or you can call toHex
        $signatureString = $signature->toHex();
       
        if(strlen((string)$v)==1){
            $v = "0".$v;
        }
        return $signatureString.$v;
    }

    /**
     * Verify whether $this sdk succ