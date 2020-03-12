            else
                    $acc = $acc->add($wnd[(-$z - 1) >> 1]->neg());
            }
        }
        return $p->type == "affine" ? $acc->toP() : $acc;
    }

    public function _wnafMulAdd($defW, $points, $coeffs, $len, $jacobianResult = false)
    {
        $wndWidth = &$this->_wnafT1;
        $wnd = &$this->_wnafT2;
        $naf = &$this->_wnafT3;

        //Fill all arrays
        $max = 0;
        for($i = 0; $i < $len; $i++)
        {
            $p = $points[$i];
            $nafPoints = $p->_getNAFPoints($defW);
            $wndWidth[$i] = $nafPoints["wnd"];
            $wnd[$i] = $nafPoints["points"];
        }
        //Comb all window NAFs
        for($i = $len - 1; $i >= 1; $i -= 2)
        {
            $a = $i - 1;
            $b = $i;
            if( $wndWidth[$a] != 1 || $wndWidth[$b] != 1 )
            {
                $naf[$a] = Utils::getNAF($coeffs[$a], $wndWidth[$a]);
                $naf[$b] = Utils::getNAF($coeffs[$b], $wndWidth[$b]);
                $max