<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Tests;

use BitWasp\Buffertools\ByteOrder;
use BitWasp\Buffertools\Types\ByteString;
use BitWasp\Buffertools\Types\Uint64;
use BitWasp\Buffertools\Types\Uint32;
use BitWasp\Buffertools\Template;
use BitWasp\Buffertools\Types\VarInt;
use BitWasp\Buffertools\Types\VarString;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Parser;

class TemplateTest extends BinaryTest
{
    public function testTemplate()
    {
        $template = new Template();
        $this->assertEmpty($template->getItems());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No items in template
     */
    public function testTemplateEmptyParse()
    {
        $template = new Template();
        $parser = new Parser('010203040a0b0c0d');
        $template->parse($parser);
    }

    public function testAddItemToTemplate()
    {
        $item = new Uint64();
        $template = new Template();

        $this->assertEmpty($template->getItems());
        $this->assertEquals(0