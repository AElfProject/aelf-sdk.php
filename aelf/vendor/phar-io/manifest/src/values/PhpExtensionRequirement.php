ags    Tag block to parse.
     * @param Types\Context $context Context of the parsed Tag
     *
     * @return DocBlock\Tag[]
     */
    private function parseTagBlock(string $tags, Types\Context $context) : array
    {
        $tags = $this->filterTagBlock($tags);
        if ($tags === null) {
            return [];
        }

        $result = [];
        $lines  = $this->splitTagBlockIntoTagLines($tags);
        foreach ($lines as $key => $tagLine) {
            $result[$key] = $this->tagFactory->create(trim($tagLine), $context);
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private function splitTagBlockIntoTagLines(string $tags) : a