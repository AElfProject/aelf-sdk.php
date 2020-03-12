y array.
     */
    public function getHeader($name)
    {
        $lowerName = strtolower($name);
        if(isset($this->headerNames[$lowerName]))
        {
            $name = $this->headerNames[$lowerName];
        }
        if(isset($this->headers[$name]))
        {
            return $this->headers[$name];
        }
        else
        {
            return [];
        }
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string o