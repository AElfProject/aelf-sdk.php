Add(BN $num) {
        if( $this->red === null )
            throw new Exception("redIAdd works only with red numbers");
        $res = $this;
        $res->bi = $res->bi->add($num->bi);
        if ($res->bi->cmp($this->red->m->bi) >= 0)
            $res->bi = $res->bi->sub($this->red->m->bi);
        return $res;
        //return $this->red->iadd($this, $num);
    }

    public function redSub(BN $num) {
        if( $this->red === null )
            throw new Exception("redSub works only with red numbers");
        $res = clone($this);
        $res->bi = $this->bi->sub($num->bi);
        if ($res->bi->sign() < 0)
            $res->bi = $res->bi->add($this->red->m->bi);
        return $res;
        //return $this->red->sub($this, $num);
    }

    public function redISub(BN $num) {
        if( $this->red === null )
            throw new Exception("redISub works only with red numbers");
        $this->bi = $this->bi->sub($num->bi);
        if ($this->bi->sign() < 0)
            $this->bi = $this->bi->add($this->red->m->bi);
        return $this;
            
//        return $this->red->isub($this, $num);
    }

    public function redShl(BN $num) {
        if( $this->red === null )
            throw new Exception("redShl works only with red numbers");
        return $this->red->shl($this, $num);
    }

    public function redMul(BN $num) {
        if( $this->red === null )
            throw new Exception("redMul works only with red numbers");
        $res = clone($this);
        $res->bi = $this->bi->mul($num->bi)->mod($this->red->m->bi);
        return $res;            
        /*
        return $this->red->mul($this, $num);
        */
    }

    public function redIMul(BN $num) {
        if( $this->red === null )
            throw new Exception("redIMul works only with red numbers");
        $this->bi = $this->bi->mul($num->bi)->mod($this->red->m->bi);
        return $this;
        //return $this->red->imul($this, $num);
    }

    public function redSqr() {
        if( $this->red === null )
            throw new Exception("redSqr works only with red numbers");
        $res = clone($this);
        $res->bi = $this->bi->mul($this->bi)->mod($this->red->m->bi);
        return $res;
        /*
        $this->red->verify1($this);
        return $this->red->sqr($this);
        */
    }

    public function redISqr() {
        if( $this->red === null )
            throw new Exception("redISqr works only with red numbers");
        $res = $this;
        $res->bi = $this->bi->mul($this->bi)->mod($this->red->m->bi);
        return $res;
/*        $this->red->verify1($this);
        return $this->red->isqr($this);
        */
    }

    public function redSqrt() {
        if( $this->red === null )
            throw new Exception("redSqrt works only with red numbers");
        $this->red->verify1($this);
        return $this->red->sqrt($this);
    }

    public function redInvm() {
        if( $this->red === null )
            throw new Exception("redInvm works only with red numbers");
        $this->red->verify1($this);
        return $this->red->invm($this);
    }

    public function redNeg() {
        if( $this->red === null )
            throw new Exception("redNeg works only with red numbers");
        $this->red->verify1($this);
        return $this->red->neg($this);
    }

    public function redPow(BN $num) {
        $this->red->verify2($this, $num);
        return $this->red->pow($this, $num);
    }

    public static function red($num) {
        return new Red($num);
    }

    public static function mont($num) {
        return new Red($num);
    }

    public function inspect() {