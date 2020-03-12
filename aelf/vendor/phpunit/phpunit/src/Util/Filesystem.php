ue, "equals true");
    test((new BigInteger(20))->equals(21), false, "equals false");
    test((new BigInteger(-20))->sign(), -1, "sign -1");
    test((new BigInteger(0))->sign(), 0, "sign 0");
    test((new BigInteger(20))->sign(), 1, "sign 1");
    testB(new BigInteger("-20"), "-20", "minus");
    testB(new BigInteger("-14", 16), "-20", "minus");
    testB(new BigInteger("-10100", 2), "-20", "minus");
}

function testBig() {
    error_log("=============\nTest big\n=============");
    $bits = "1001010111010010100001000101110110100001000101101000110101010101001";
    $hex = "eeaf0ab9adb38dd69c33f80afa8fc5e86072618775ff3c0b9ea2314c9c256576d674df7496ea81d3383b4813d692c6e0e0d5d8e250b98be48e495c1d6089dad15dc7d7b46154d6b6ce8ef4ad69b15d4982559b297bcf1885c529f566660e57ec68edbc3c05726cc02fd4cbf4976eaa9afd513