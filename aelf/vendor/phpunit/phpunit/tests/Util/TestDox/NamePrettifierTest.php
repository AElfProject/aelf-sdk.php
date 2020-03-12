clone($this);
        $res->bi = $res->bi->pow($num->bi);
        return $res;
    }

    // Shift-left in-place
    public function iushln($bits) {
        assert(is_integer($bits) && $bits >= 0);
        if ($bits < 54) {
            $this->bi = $this->bi->mul(1 << $bits);
        } else {
            $this->bi = $this->bi->mul((new BigInteger(2))->pow($bits));
        }
        return $this;
    }

    public function ishln($bits) {
        if (assert_options(ASSERT_ACTIVE)) assert(!$this->negative());
        return $this->iushln($bits);
    }

    // Shift-right in-place
    // NOTE: `hint` is a lowest bit before trailing zeroes
    // NOTE: if `extended` is present - it will be filled with destroyed bits
    public function iushrn($bits, $hint = 0, &$extended = null) {
        if( $hint != 0 )
            throw new Exception("Not implemented");

        assert(is_integer($bits) && $bits >= 0);

        if( $extended != null )
            $extended = $this->maskn($bits);
               
        if ($bits < 54) {
            $this->bi = $this->bi->div(1 << $bits);
        } else {
            $this->bi = $this->bi->div((new BigInteger(2))->pow($bits));
        }
        return $this;
    }

    public function ishrn($bits, $hint = null, $extended = null) {
        if (assert_options(ASSERT_ACTIVE)) assert(!$this->negative());
        return $this->iushrn($bits, $hint, $extended);
    }

    // Shift-left
    public function shln($bits) {
        return $this->_clone()->ishln($bits);
    }

    public function ushln($bits) {
        return $this->_clone()->iushln($bits);
    }

    // Shift-right
    public function shrn($bits) {
        return $this->_clone()->ishrn($bits);
    }

    public function ushrn($bits) {
        return $this->_clone()->iushrn($bits);
    }

    // Test if n bit is set
    public function testn($bit) {
        assert(is_integer($bit) && $bit >= 0);
        return $this->bi->testbit($bit);
    }

    // Return only lowers bits of number (in-place)
    public function imaskn($bits) {
        assert(is_integer($bits) && $bits >= 0);
        if (assert_options(ASSERT_ACTIVE)) assert(!$this->negative());
        $mask = "";
        for($i = 0; $i < $bits; $i++)
            $mask .= "1";
        return $this->iand(new BN($mask, 2));
    }

    // Return only lowers bits of number
    public function maskn($bits) {
        return $this->_clone()->imaskn($bits);
    }

    // Add plain number `num` to `this`
    public function iaddn($num) {
        assert(is_numeric($num));
        $this->bi = $this->bi->add(intval($num));
        return $this;
    }

    // Subtract plain number `num` from `this`
    public function isubn($num) {
        assert(is_numeric($num));
        $this->bi = $this->bi->sub(intval($num));
        return $this;
    }

    public function addn($num) {
        return $this->_clone()->iaddn($num);
    }

 