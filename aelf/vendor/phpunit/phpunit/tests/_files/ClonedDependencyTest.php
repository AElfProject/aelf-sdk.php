info->getRelativeLockTime() & TransactionInput::SEQUENCE_LOCKTIME_DISABLE_FLAG) != 0) {
                return;
            }

            if (!$this->signatureChecker->checkSequence(Number::int($info->getRelativeLockTime()))) {
                if ($this->tx->getVersion() < 2) {
                    throw new \RuntimeException("Transaction version must be 2 or greater for CSV");
                }

                $input = $this->tx->getInput($this->nInput);
                if ($input->isFinal()) {
                    throw new \RuntimeException("Sequence LOCKTIME_DISABLE_FLAG is set - not allowed on CSV output");
                }

                $cmp = $this->compareRangeAgainstThreshold($info->getRelativeLockTime(), $input->getSequence(), TransactionInput::SEQUENCE_LOCKTIME_TYPE_FLAG);
          