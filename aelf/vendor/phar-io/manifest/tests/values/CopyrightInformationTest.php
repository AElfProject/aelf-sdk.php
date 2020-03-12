<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Tests;

use \BitWasp\Buffertools\Buffer;
use \BitWasp\Buffertools\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParserEmpty()
    {
        $parser = new Parser();
        $this->assertInstanceOf(Parser::class, $parser);

        $this->assertSame(0, $parser->getPosition());
        $this->assertInstanceOf(Buffer::class, $parser->getBuffer());
        $this->assertEmpty($parser->getBuffer()->getHex());
    }

    public function testGetBuffer()
    {
        $buffer = Buffer::hex('41414141');

        $parser = new Parser($buffer);
        $this->assertSame($parser->getBuffer()->getBinary(), $buffer->getBinary());
    }

    public function testGetBufferEmptyNull()
    {
        $buffer = new Buffer();
        $parser = new Parser($buffer);
        $parserData = $parser->getBuffer()->getBinary();
        $bufferData = $buffer->getBinary();
        $this->assertSame($parserData, $bufferData);
    }

    public function testWriteBytes()
    {
        $bytes = '41424344';
        $parser = new Parser();
        $parser->writeBytes(4, Buffer::hex($bytes));
        $returned = $parser->getBuffer()->getHex();
        $this->assertSame($returned, '41424344');
    }

    public function testWriteBytesFlip()
    {
        $bytes = '41424344';
        $parser = new Parser();
        $parser->writeBytes(4, Buffer::hex($bytes), true);
        $returned = $parser->getBuffer()->getHex();
        $this->assertSame($returned, '44434241');
    }

    public function testWriteBytesPadded()
    {
        $parser = new Parser();
        $parser->writeBytes(4, Buffer::hex('34'));
        $this->assertEquals("00000034", $parser->getBuff