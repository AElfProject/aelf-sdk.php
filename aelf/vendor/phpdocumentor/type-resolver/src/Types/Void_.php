s("0011", $a->slice(2, 4)->getHex());

        $b = Buffer::hex("00111100");
        $this->assertEquals("0011", $b->slice(0, 2)->getHex());
        $this->assertEquals("1100", $b->slice(2, 4)->getHex());

        $c = Buffer::hex("111100", 4);
        $this->assertEquals("0011", $c->slice(0, 2)->getHex());
        $this->assertEquals("1100", $c->slice(2, 4)->getHex());
    }

    public function testEquals()
    {
        $first = Buffer::hex('ab');
        $second = Buffer::hex('ab');
        $firstExtraLong = Buffer::hex('ab', 10);
        $firstShort = new Buffer('', 0);
        $this->assertTrue($first->equals($second));
        $this->assertFalse($first->equals($firstExtraLong));
        $this->assertFalse($first->equals($