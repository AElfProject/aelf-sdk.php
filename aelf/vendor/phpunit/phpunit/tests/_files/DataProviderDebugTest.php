                } else {
                            $step = new Conditional($op->getOp());
                        }

                        $steps[] = $step;

                        if ($op->getOp() === Opcodes::OP_NOTIF) {
                            $value = !$value;
                        }

                        $vfStack->push($value);
                        break;
                    case Opcodes::OP_ENDIF:
                        $vfStack->pop();
                        break;
                    case Opcodes::OP_ELSE:
                        $vfStack->push(!$vfStack->pop());
                        break;
                }
            } else {
                $templateTypes = $this->parseSequence($scriptSection);

                // Detect if effect on mainStack is `false`
                $resolvesFalse = count($pathCopy) > 0 && !$pathCopy[0];
                if ($resolvesFalse) {
                    if (count($templateTypes) > 1) {
                        throw new UnsupportedScript("Unsupported script, multiple steps to segment which is negated");
                    }
                }

                foreach ($templateTypes as $k => $checksig) {
                    if ($fExec) {
                        if ($checksig instanceof Checksig) {
                            $this->extractChecksig($signScript->getScript(), $checksig, $stack, $this->fqs->sigVersion(), $resolvesFalse);

                            // If this statement results is later co