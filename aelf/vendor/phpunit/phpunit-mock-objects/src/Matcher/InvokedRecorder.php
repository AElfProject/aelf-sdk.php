if ($this->current === $this->buffer_end) {
            // Make sure that it failed due to EOF, not because we hit
            // total_bytes_limit, which, unlike normal limits, is not a valid
            // place to end a message.
            $current_position = $this->total_bytes_read -
                $this->buffer_size_after_limit;
            if ($current_position >= $this->total_bytes_limit) {
                // Hit total_bytes_limit_.  But if we also hit the normal limit,
                // we're still OK.
                $this->legitimate_message_end =
                    ($this->current_limit === $this->total_bytes_limit);
            } else {
                $this->legitimate_message_end = true;
            }
            return 0;
        }

        $result = 0;
        // The largest tag is 2^29 - 1, which can be represented by int32.
        $success = $this->readVarint32($result);
        if ($success) {
            return $result;
        } else {
            return 0;
        }
    }

    public function readRaw($size, &$buffer)
    {
        $current_buffer_size = 0;
        if ($this->bufferSize() < $size) {
            return false;
        }

        if ($size === 0) {
          $buffer = "";
        } else {
          $buffer = substr($this->buffer, $this->current, $size);
          $this->advance($size);
        }

        return true;
    }

    /* Places a limit on the number of bytes that the stream may read, starting
     * from the current position.  Once the stream hits this limit, it will act
     * like the end of the i