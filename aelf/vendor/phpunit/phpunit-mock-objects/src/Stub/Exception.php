<?php

use StephenHill\Base58;
use StephenHill\BCMathService;
use StephenHill\GMPService;

class Base58Tests extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider encodingsProvider
     */
    public function testEncode($string, $encoded, $instance)
    {
        $string = (string) $string;
        $encoded = (string) $encoded;

        $this->assertSame($encoded, $instance->encode($string));
    }

    /**
     * @dataProvider encodingsProvider
     */
    public function testDecode($string, $encoded, $instance)
    {
        $string = (string) $string;
        $encoded = (string) $encoded;

        $this->assertSame($string, $instance->decode($encoded));
    }

    public function encodingsProvider()
    {
        $instances = array(
            new Base58(null, new BCMathService()),
            new Base58(null, new GMPService())
        );

        $tests = array(
            array('', ''),
            array('1', 'r'),
            array('a', '2g'),
            array('bbb', 'a3gV'),
            array('ccc', 'aPEr'),
            array('hello!', 'tzCkV5Di'),
            array('Hello World', 'JxF12TrwUP45BMd'),
            array('this is a test', 'jo91waLQA1NNeBmZKUF'),
            array('the qu