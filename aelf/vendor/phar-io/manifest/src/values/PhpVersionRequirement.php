', '$1', $comment);
        Assert::string($comment);
        $comment = trim($comment);

        // reg ex above is not able to remove */ from a single line docblock
        if (substr($comment, -2) === '*/') {
            $comment = trim(substr($comment, 0, -2));
        }

        return str_replace(["\r\n", "\r"], "\n", $comment);
    }

    // phpcs:disable
    /**
     * Splits the DocBlock into a template marker, summary, description and block of tags.
     *
     * @param string $comment Comment to split into the sub-parts.
     *
     * @return string[] containing the template marker (if any), summary, description and a string containing the tags.
     *
     * @author Mike van Riel <me@mikevanriel.com> for extending the regex with template marker sup