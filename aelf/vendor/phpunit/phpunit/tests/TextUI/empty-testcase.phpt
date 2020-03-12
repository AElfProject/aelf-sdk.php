hod MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name)
    {
        $self = clone $this;
        $lowerName = strtolower($name);
        if(isset($self->headerNames[$lowerName]))
        {
            $name = $self->headerNames[$lowerName];
        }
        if(isset($self->headers[$name]))
        {
            unset($self->headers[$name]);
        }