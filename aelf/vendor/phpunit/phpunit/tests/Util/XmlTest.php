   return $num->_clone()->ior($this);
    }

    public function uor(BN $num) {
        if( $this->ucmp($num) > 0 )
            return $this->_clone()->iuor($num);
        return $num->_clone()->ior($this);
    }

    // And `num` with `this` in-place
    public function iuand(BN $num) {
        $this->bi = $this->bi->binaryAnd($num->bi);
        return $this;
    }

    public function iand(BN $num) {
        if (assert_options(ASSERT_ACTIVE)) assert(!$this->negative() && !$num->negative());
        return $this->iuand($num);
    }

    // And `num` with `this`
    public function _and(BN $num) {
        if( $this->ucmp($num) > 0 )
            return $this->_clone()->iand($num);
        return $num->_clone()->iand($this);
    }

    public function uand(BN $num) {
        if( $this->ucmp($num) > 0 )
            return $this->_clone()->iuand($num);
        return $num->_clone()->iuand($this);
    }

    // Xor `num` with `this` in-place
    public function iuxor(BN $num) {
        $this->bi = $this->bi->binaryXor($num->bi);
        return $this;
    }

    public function ixor(BN $num) {
        if (assert_options(ASSERT_ACTIVE)) assert(!$this->negative() && !$num->negative());
        return $this->iuxor($num);
    }

    // Xor `num` with `this`
    public function _xor(BN $num) {
        if( $this->ucmp($num) > 0 )
            return $this->_clone()->ixor($num);
        return $num->_clone()->ixor($this);
    }

    public function uxor(BN $num) {
        if( $this->ucmp($num) > 0 )
            return $this->_clone()->iuxor($num);
        return $num->_clone()->iuxor($this);
    }

    // Not ``this`` with ``width`` bitwidth
    public function inotn($width)
    {
        assert(is_integer($width) && $width >= 0);
        $neg = false;
        if( $this->isNeg() )
        {
            $this->negi();
            $neg = true;
        }

        for($i = 0; $i < $width; $i++)
            $this->bi = $this->bi->setbit($i, !$this->bi->testbit($i));

        return $neg ? $this->negi() : $this;
    }

    public function notn($width) {
        return $this->_clone()->inotn($width);
    }

    // Set `bit` of `this`
    public function setn($bit, $val) {
        assert(is_integer($bit) && $bit > 0);
        $this->bi 