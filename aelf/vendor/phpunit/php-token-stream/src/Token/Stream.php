     break;

                        case Opcodes::OP_NOP1:
                        case Opcodes::OP_NOP4:
                        case Opcodes::OP_NOP5:
                        case Opcodes::OP_NOP6:
                        case Opcodes::OP_NOP7:
                        case Opcodes::OP_NOP8:
                        case Opcodes::OP_NOP9:
                        case Opcodes::OP_NOP10:
                            if ($flags & self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS) {
                                throw new ScriptRuntimeException(self::VERIFY_DISCOURAGE_UPGRADABLE_NOPS, 'Upgradable NOP found - this is discouraged');
                            }
                            break;

                        case Opcodes::OP_NOP:
                            break;

                        case Opcodes::OP_IF:
                        case Opcodes::OP_NOTIF:
                            // <expression> if [statements] [else [statements]] endif
                            $value = false;
                            if ($fExec) {
                                if ($mainStack->isEmpty()) {
                                    throw new \RuntimeException('Unbalanced conditional');
                                }
                                $vch = $mainStack[-1];

                                if ($sigVersion === SigHash::V1 && ($flags & self::VERIFY_MINIMALIF)) {
                                    if ($vch->getSize() > 1) {
                                        throw new ScriptRuntimeException(self::VERIFY_MINIMALIF, 'Input to OP_IF/NOTIF should be minimally encoded');
                                    }

                                    if ($vch->getSize() === 1 && $vch->getBinary() !== "\x01") {
                                        throw new ScriptRuntimeException(self::VERIFY_MINIMALIF, 'Input to OP_IF/NOTIF should be minimally encoded');
                                    }
                                }

                                $buffer = Number::buffer($mainStack->pop(), $minimal)->getBuffer();
                                $value = $this->castToBool($buffer);
                                if ($opCode === Opcodes::OP_NOTIF) {
                                    $value = !$value;
                                }
                            }
                            $vfStack->push($value);
                            break;

                        case Opcodes::OP_ELSE:
                            if ($vfStack->isEmpty()) {
                                throw new \RuntimeException('Unbalanced conditional');
                            }
                            $vfStack->push(!$vfStack->pop());
                            break;

                        case Opcodes::OP_ENDIF:
                            if ($vfStack->isEmpty()) {
                                throw new \RuntimeException('Unbalanced conditional');
                            }
                            $vfStack->pop();
                            break;

                        case Opcodes::OP_VERIFY:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }
                            $value = $this->castToBool($mainStack[-1]);
                            if (!$value) {
                                throw new \RuntimeException('Error: verify');
                            }
                            $mainStack->pop();
                            break;

                        case Opcodes::OP_TOALTSTACK:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_TOALTSTACK');
                            }
                            $altStack->push($mainStack->pop());
                            break;

                        case Opcodes::OP_FROMALTSTACK:
                            if ($altStack->isEmpty()) {
                                throw new \RuntimeException('Invalid alt-stack operation OP_FROMALTSTACK');
                            }
                            $mainStack->push($altStack->pop());
                            break;

                        case Opcodes::OP_IFDUP:
                            // If top value not zero, duplicate it.
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_IFDUP');
                            }
                            $vch = $mainStack[-1];
                            if ($this->castToBool($vch)) {
                                $mainStack->push($vch);
                            }
                            break;

                        case Opcodes::OP_DEPTH:
                            $num = count($mainStack);
                            $depth = Number::int($num)->getBuffer();
                            $mainStack->push($depth);
                            break;

                        case Opcodes::OP_DROP:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_DROP');
                            }
                            $mainStack->pop();
                            break;

                        case Opcodes::OP_DUP:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_DUP');
                            }
                            $vch = $mainStack[-1];
                            $mainStack->push($vch);
                            break;

                        case Opcodes::OP_NIP:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_NIP');
                            }
                            unset($mainStack[-2]);
                            break;

                        case Opcodes::OP_OVER:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_OVER');
                            }
                            $vch = $mainStack[-2];
                            $mainStack->push($vch);
                            break;

                        case Opcodes::OP_ROT:
                            if (count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation OP_ROT');
                            }
                            $mainStack->swap(-3, -2);
                            $mainStack->swap(-2, -1);
                            break;

                        case Opcodes::OP_SWAP:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_SWAP');
                            }
                            $mainStack->swap(-2, -1);
                            break;

                        case Opcodes::OP_TUCK:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_TUCK');
                            }
                            $vch = $mainStack[-1];
                            $mainStack->add(- 2, $vch);
                            break;

                        case Opcodes::OP_PICK:
                        case Opcodes::OP_ROLL:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_PICK');
                            }

                            $n = Number::buffer($mainStack[-1], $minimal, 4)->getGmp();
                            $mainStack->pop();
                            if ($this->math->cmp($n, $zero) < 0 || $this->math->cmp($n, gmp_init(count($mainStack))) >= 0) {
                                throw new \RuntimeException('Invalid stack operation OP_PICK');
                            }

                            $pos = (int) gmp_strval($this->math->sub($this->math->sub($zero, $n), gmp_init(1)), 10);
                            $vch = $mainStack[$pos];
                            if ($opCode === Opcodes::OP_ROLL) {
                                unset($mainStack[$pos]);
                            }
                            $mainStack->push($vch);
                            break;

                        case Opcodes::OP_2DROP:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_2DROP');
                            }
                            $mainStack->pop();
                            $mainStack->pop();
                            break;

                        case Opcodes::OP_2DUP:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_2DUP');
                            }
                            $string1 = $mainStack[-2];
                            $string2 = $mainStack[-1];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;

                        case Opcodes::OP_3DUP:
                            if (count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation OP_3DUP');
                            }
                            $string1 = $mainStack[-3];
                            $string2 = $mainStack[-2];
                            $string3 = $mainStack[-1];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            $mainStack->push($string3);
                            break;

                        case Opcodes::OP_2OVER:
                            if (count($mainStack) < 4) {
                                throw new \RuntimeException('Invalid stack operation OP_2OVER');
                            }
                            $string1 = $mainStack[-4];
                            $string2 = $mainStack[-3];
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;

                        case Opcodes::OP_2ROT:
                            if (count($mainStack) < 6) {
                                throw new \RuntimeException('Invalid stack operation OP_2ROT');
                            }
                            $string1 = $mainStack[-6];
                            $string2 = $mainStack[-5];
                            unset($mainStack[-6], $mainStack[-5]);
                            $mainStack->push($string1);
                            $mainStack->push($string2);
                            break;

                        case Opcodes::OP_2SWAP:
                            if (count($mainStack) < 4) {
                                throw new \RuntimeException('Invalid stack operation OP_2SWAP');
                            }
                            $mainStack->swap(-3, -1);
                            $mainStack->swap(-4, -2);
                            break;

                        case Opcodes::OP_SIZE:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation OP_SIZE');
                            }
                            $size = Number::int($mainStack[-1]->getSize());
                            $mainStack->push($size->getBuffer());
                            break;

                        case Opcodes::OP_EQUAL:
                        case Opcodes::OP_EQUALVERIFY:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation OP_EQUAL');
                            }

                            $equal = $mainStack[-2]->equals($mainStack[-1]);
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($equal ? $this->vchTrue : $this->vchFalse);
                            if ($opCode === Opcodes::OP_EQUALVERIFY) {
                                if ($equal) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('Error EQUALVERIFY');
                                }
                            }

                            break;

                        // Arithmetic operations
                        case $opCode >= Opcodes::OP_1ADD && $opCode <= Opcodes::OP_0NOTEQUAL:
                            if ($mainStack->isEmpty()) {
                                throw new \Exception('Invalid stack operation 1ADD-OP_0NOTEQUAL');
                            }

                            $num = Number::buffer($mainStack[-1], $minimal)->getGmp();

                            if ($opCode === Opcodes::OP_1ADD) {
                                $num = $this->math->add($num, gmp_init(1));
                            } elseif ($opCode === Opcodes::OP_1SUB) {
                                $num = $this->math->sub($num, gmp_init(1));
                            } elseif ($opCode === Opcodes::OP_2MUL) {
                                $num = $this->math->mul(gmp_init(2), $num);
                            } elseif ($opCode === Opcodes::OP_NEGATE) {
                                $num = $this->math->sub($zero, $num);
                            } elseif ($opCode === Opcodes::OP_ABS) {
                                if ($this->math->cmp($num, $zero) < 0) {
                                    $num = $this->math->sub($zero, $num);
                                }
                            } elseif ($opCode === Opcodes::OP_NOT) {
                                $num = gmp_init($this->math->cmp($num, $zero) === 0 ? 1 : 0);
                            } else {
                                // is OP_0NOTEQUAL
                                $num = gmp_init($this->math->cmp($num, $zero) !== 0 ? 1 : 0);
                            }

                            $mainStack->pop();

                            $buffer = Number::int(gmp_strval($num, 10))->getBuffer();

                            $mainStack->push($buffer);
                            break;

                        case $opCode >= Opcodes::OP_ADD && $opCode <= Opcodes::OP_MAX:
                            if (count($mainStack) < 2) {
                                throw new \Exception('Invalid stack operation (OP_ADD - OP_MAX)');
                            }

                            $num1 = Number::buffer($mainStack[-2], $minimal)->getGmp();
                            $num2 = Number::buffer($mainStack[-1], $minimal)->getGmp();

                            if ($opCode === Opcodes::OP_ADD) {
                                $num = $this->math->add($num1, $num2);
                            } else if ($opCode === Opcodes::OP_SUB) {
                                $num = $this->math->sub($num1, $num2);
                            } else if ($opCode === Opcodes::OP_BOOLAND) {
                                $num = (int) ($this->math->cmp($num1, $zero) !== 0 && $this->math->cmp($num2, $zero) !== 0);
                            } else if ($opCode === Opcodes::OP_BOOLOR) {
                                $num = (int) ($this->math->cmp($num1, $zero) !== 0 || $this->math->cmp($num2, $zero) !== 0);
                            } elseif ($opCode === Opcodes::OP_NUMEQUAL) {
                                $num = (int) ($this->math->cmp($num1, $num2) === 0);
                            } elseif ($opCode === Opcodes::OP_NUMEQUALVERIFY) {
                                $num = (int) ($this->math->cmp($num1, $num2) === 0);
                            } elseif ($opCode === Opcodes::OP