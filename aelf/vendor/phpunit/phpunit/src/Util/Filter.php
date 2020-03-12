<?php

use BI\BigInteger;

function test($a, $b, $message = "") {
    error_log(($a === $b ? "PASS" : "FAIL get: " . $a . ", expected: " . $b) . " " . $message);
}

function testB($a, $b, $message = "") {
    error_log(($a->toString() === $b ? "PASS" : "FAIL get: " . $a . ", expected: " . $b) . " " . $message);
}

function testSerialization($b, $msg = "") {
    test($b->toBits(), "1010000", $msg . " toBits");
    test($b->toBytes(), hex2bin("50"), $msg . " toBytes");
    test($b->toHex(), "50", $msg . " toHex");
    test($b->toDec(), "80", $msg . " toDec");
    test($b->toNumber(), 80, $msg . " toNumber");
    test($b->toBase(58), "1M", $msg . " to58");
}

function testCreate() {
    error_log("=============\nTest serialization\n=============");
    testSerialization(new BigInteger("1010000", 2), "bits");
    testSerialization(new BigInteger(hex2bin("50"), 256), "bytes");
    testSerialization(new BigInteger("50", 16), "hex");
    testSerialization(new BigInteger("80", 10), "dec");
    testSerialization(new BigInteger("80"), "dec2");
    testSerialization(new BigInteger(80), "number");
}

function testCreateSafeSingle($value, $base, $msg) {
    try {
        $z = new BigInteger($value, $base);
        error_log("FAIL exception during create " . $msg);
    }
    catch (\Exception $e) {
        error_log("PASS exception during create " . $msg);
    }
    test(BigInteger::createSafe($value, $base), false, "createSafe " . $msg);
}

function testCreateSafe() {
    error_log("=============\nTest create safe\n=============");
    testCreateSafeSingle("zz", 2, "bin");
    testCreateSafeSingle("zz", 10, "dec");
    testCreateSafeSingle("zz", 16, "hex");
}

function testSpaces() {
    error_log("=============\nTest spaces\n=============");
    test((new BigInteger("11  0   1", 2))->toBits(), "1101", "bin");
    test((new BigInteger("6   2 0  6", 10))->toDec(), "6206", "dec");
    test((new BigInteger("f3 5  12 ac 0", 16))->toHex(), "f3512ac0", "hex");
}

function testOp() {
    error_log("=============\nTest op\n=============");
    testB((new BigInteger(20))->add(34), "54", "add");
    testB((new BigInteger(20))->sub(14), "6", "sub");
    testB((new BigInteger(20))->mul(12), "240", "mul");
    testB((new BigInteger(20))->div(4), "5", "div");
    testB((new BigInteger(20))->divR(7), "6", "divR");
    $qr = (new BigInteger(20))->divQR(6);
    testB($qr[0], "3", "divQR[0]");
    testB($qr[1], "2", "divQR[1]");
    testB((new BigInteger(20))->mod(3), "2", "mod");
    testB((new BigInteger(54))->gcd(81), "27", "gcd");
    testB((new BigInteger(3))->modInverse(10), "7", "modInverse");
    testB((new BigInteger(3))->pow(4), "81", "pow");
    testB((new BigInteger(3))->powMod(4, 10), "1", "powMod");
    testB((new BigInteger(20))->abs(), "20", "abs");
    testB((new BigInteger(20))->neg(), "-20", "neg");
    testB((new BigInteger(20))->binaryAnd(18), "16", "binaryAnd");
    testB((new BigIn