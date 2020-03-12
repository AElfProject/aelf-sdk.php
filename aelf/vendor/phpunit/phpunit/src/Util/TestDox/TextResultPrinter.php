     * Add a little-endian Int8 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int8le(): TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }

    /**
     * Add a int16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int16(): TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }

    /**
     * Add a little-endian Int16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int16le(): TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }

    /**
     * Add a int32 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int32(): TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }

    /**
     * Add a little-endian Int serializer to the template
     *
    