N $num)
    {
        if (assert_options(ASSERT_ACTIVE)) assert(!$num->isZero());

        $negative = $this->negative() !== $num->negative();

        $res = $this->_clone()->abs();
        $arr = $res->bi->divQR($num->bi->abs());
        $res->bi = $arr[0];
        $tmp = $num->bi->sub($arr[1]->mul(2));
        if( $tmp->cmp(0) <= 0 && (!$negative || $this->negative() === 0) )
            $res->iaddn(1);
        return $negative ? $res->negi() : $res;
    }

    public function modn($num) {
        assert(is_numeric($num) && $num != 0);
        return $this->bi->divR(intval($num))->toNumber();
    }

    // In-place division by number
    public function idivn($num) {
        assert(is_numeric($num) && $num != 0);
        $this->bi = $this->bi->div(intval($num));
        return $this;
    }

    public function divn($num) {
        return $this->_clone()->idivn($num);
    }

    public function gcd(BN $num) {
        $res = clone($this);
        $res->bi = $this->bi->gcd($num->bi);
        return $res;
    }

    public function invm(BN $num) {
        $res = clone($this);
        $res->bi = $res->bi->modInverse($num->bi);
        return $res;
    }

    public function isEven() {
        return !$this->bi->testbit(0)