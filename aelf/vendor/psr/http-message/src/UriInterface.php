                } elseif ($opCode === Opcodes::OP_GREATERTHAN) {
                                $num = (int) ($this->math->cmp($num1, $num2) > 0);
                            } elseif ($opCode === Opcodes::OP_LESSTHANOREQUAL) {
                                $num = (int) ($this->math->cmp($num1, $num2) <= 0);
                            } elseif ($opCode === Opcodes::OP_GREATERTHANOREQUAL) {
                                $num = (int) ($this->math->cmp($num1, $num2) >= 0);
                            } elseif ($opCode === Opcodes::OP_MIN) {
                                $num = ($this->math->cmp($num1, $num2) <= 0) ? $num1 : $num2;
                            } else {
                                $num = ($this->math->cmp($num1, $num2) >= 0) ? $num1 : $num2;
                            }

                            $mainStack->pop();
                            $mainStack->pop();
                            $buffer = Number::int(gmp_strval($num, 10))->getBuffer();
                            $mainStack->push($buffer);

                            if ($opCode === Opcodes::OP_NUMEQUALVERIFY) {
                                if ($this->castToBool($mainStack[-1])) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('NUM EQUAL VERIFY error');
                                }
                            }
                            break;

                        case Opcodes::OP_WITHIN:
                            if (count($mainStack) < 3) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            $num1 = Number::buffer($mainStack[-3], $minimal)->getGmp();
                            $num2 = Number::buffer($mainStack[-2], $minimal)->getGmp();
                            $num3 = Number::buffer($mainStack[-1], $minimal)->getGmp();

                            $value = $this->math->cmp($num2, $num1) <= 0 && $this->math->cmp($num1, $num3) < 0;
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($value ? $this->vchTrue : $this->vchFalse);
                            break;

                        // Hash operation
                        case Opcodes::OP_RIPEMD160:
                        case Opcodes::OP_SHA1:
                        case Opcodes::OP_SHA256:
                        case Opcodes::OP_HASH160:
                        case Opcodes::OP_HASH256:
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            $buffer = $mainStack[-1];
                            if ($opCode === Opcodes::OP_RIPEMD160) {
                                $hash = Hash::ripemd160($buffer);
                            } elseif ($opCode === Opcodes::OP_SHA1) {
                                $hash = Hash::sha1($buffer);
                            } elseif ($opCode === Opcodes::OP_SHA256) {
                                $hash = Hash::sha256($buffer);
                            } elseif ($opCode === Opcodes::OP_HASH160) {
                                $hash = Hash::sha256ripe160($buffer);
                            } else {
                                $hash = Hash::sha256d($buffer);
                            }

                            $mainStack->pop();
                            $mainStack->push($hash);
                            break;

                        case Opcodes::OP_CODESEPARATOR:
                            $hashStartPos = $parser->getPosition();
                            break;

                        case Opcodes::OP_CHECKSIG:
                        case Opcodes::OP_CHECKSIGVERIFY:
                            if (count($mainStack) < 2) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            $vchPubKey = $mainStack[-1];
                            $vchSig = $mainStack[-2];

                            $scriptCode = new Script($script->getBuffer()->slice($hashStartPos));

                            $success = $checker->checkSig($scriptCode, $vchSig, $vchPubKey, $sigVersion, $flags);

                            $mainStack->pop();
                            $mainStack->pop();
                            $mainStack->push($success ? $this->vchTrue : $this->vchFalse);

                            if (!$success && ($flags & self::VERIFY_NULLFAIL) && $vchSig->getSize() > 0) {
                                throw new ScriptRuntimeException(self::VERIFY_NULLFAIL, 'Signature must be zero for failed OP_CHECK(MULTIS)SIG operation');
                            }

                            if ($opCode === Opcodes::OP_CHECKSIGVERIFY) {
                                if ($success) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('Checksig verify');
                                }
                            }
                            break;

                        case Opcodes::OP_CHECKMULTISIG:
                        case Opcodes::OP_CHECKMULTISIGVERIFY:
                            $i = 1;
                            if (count($mainStack) < $i) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            $keyCount = Number::buffer($mainStack[-$i], $minimal)->getInt();
                            if ($keyCount < 0 || $keyCount > 20) {
                                throw new \RuntimeException('OP_CHECKMULTISIG: Public key count exceeds 20');
                            }

                            $opCount += $keyCount;
                            $this->checkOpcodeCount($opCount);

                            // Extract positions of the keys, and signatures, from the stack.
                            $ikey = ++$i;
                            $ikey2 = $keyCount + 2;
                            $i += $keyCount;
                            if (count($mainStack) < $i) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            $sigCount = Number::buffer($mainStack[-$i], $minimal)->getInt();
                            if ($sigCount < 0 || $sigCount > $keyCount) {
                                throw new \RuntimeException('Invalid Signature count');
                            }

                            $isig = ++$i;
                            $i += $sigCount;

                            // Extract the script since the last OP_CODESEPARATOR
                            $scriptCode = new Script($script->getBuffer()->slice($hashStartPos));

                            $fSuccess = true;
                            while ($fSuccess && $sigCount > 0) {
                                // Fetch the signature and public key
                                $sig = $mainStack[-$isig];
                                $pubkey = $mainStack[-$ikey];

                                if ($checker->checkSig($scriptCode, $sig, $pubkey, $sigVersion, $flags)) {
                                    $isig++;
                                    $sigCount--;
                                }

                                $ikey++;
                                $keyCount--;

                                // If there are more signatures left than keys left,
                                // then too many signatures have failed. Exit early,
                                // without checking any further signatures.
                                if ($sigCount > $keyCount) {
                                    $fSuccess = false;
                                }
                            }

                            while ($i-- > 1) {
                                // If the operation failed, we require that all signatures must be empty vector
                                if (!$fSuccess && ($flags & self::VERIFY_NULLFAIL) && !$ikey2 && $mainStack[-1]->getSize() > 0) {
                                    throw new ScriptRuntimeException(self::VERIFY_NULLFAIL, 'Bad signature must be empty vector');
                                }

                                if ($ikey2 > 0) {
                                    $ikey2--;
                                }

                                $mainStack->pop();
                            }

                            // A bug causes CHECKMULTISIG to consume one extra argument
                            // whose contents were not checked in any way.
                            //
                            // Unfortunately this is a potential source of mutability,
                            // so optionally verify it is exactly equal to zero prior
                            // to removing it from the stack.
                            if ($mainStack->isEmpty()) {
                                throw new \RuntimeException('Invalid stack operation');
                            }

                            if ($flags & self::VERIFY_NULL_DUMMY && $mainStack[-1]->getSize() !== 0) {
                                throw new ScriptRuntimeException(self::VERIFY_NULL_DUMMY, 'Extra P2SH stack value should be OP_0');
                            }

                            $mainStack->pop();
                            $mainStack->push($fSuccess ? $this->vchTrue : $this->vchFalse);

                            if ($opCode === Opcodes::OP_CHECKMULTISIGVERIFY) {
                                if ($fSuccess) {
                                    $mainStack->pop();
                                } else {
                                    throw new \RuntimeException('OP_CHECKMULTISIG verify');
                                }
                            }
                            break;

                        default:
                            throw new \RuntimeException('Opcode not found');
                    }

                    if (count($mainStack) + count($altStack) > 1000) {
                        throw new \RuntimeException('Invalid stack size, exceeds 1000');
                    }
                }
            }

            if (count($vfStack) !== 0) {
                throw new \RuntimeException('Unbalanced conditional at script end');
            }

            return true;
        } catch (ScriptRuntimeException $e) {
            // echo "\n Runtime: " . $e->getMessage() . "\n" . $e->getTraceAsString() . PHP_EOL;
            // Failure due to script tags, can access flag: $e->getFailureFlag()
            return false;
        } catch (\Exception $e) {
            // echo "\n General: " . $e->getMessage()  . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
            return false;
        }
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     INDX( 	 uÔ
Ù           (   À  è             Õs Õ          Æ»    x h     ±»    ñ ¦/&øÕ¯M¦/&øÕ¯M¦/&øÕ¯M¦/&øÕ        µ              F i e l d D e s c r i p t o r . p h p È»    € j     ±»    ]ÿµ/&øÕP½/&øÕP½/&øÕP½/&øÕ                       F i e l d D e s c r i p t o r P r o t o . p h Ç»   