s streamed.
     *
     * Generated from protobuf field <code>bool request_streaming = 3;</code>
     * @param bool $var
     * @return $this
     */
    public function setRequestStreaming($var)
    {
        GPBUtil::checkBool($var);
        $this->request_streaming = $var;

        return $this;
    }

    /**
     * The URL of the output message type.
     *
     * Generated from protobuf field <code>string response_type_url = 4;</code>
     * @return string
     */
    public function getResponseTypeUrl()
    {
        return $this->response_type_url;
    }

    /**
     * The URL of the output message type.
     *
     * Generated from protobuf field <code>string response_type_url = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setResponseTypeUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->response_type_url = $var;

        return $this;
    }

    /**
     * If true, the response is streamed.
     *
     * Generated from protobuf field <code>bool response_streaming = 5;</code>
     * @return bool
     */
    public function getResponseStreaming()
    {
        return $this->response_streaming;
    }

    /**
     * If true, the response is streamed.
     *
     *