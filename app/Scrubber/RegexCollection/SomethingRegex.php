<?php

namespace App\Scrubber\RegexCollection;

use YorCreative\Scrubber\Interfaces\RegexCollectionInterface;

class SomethingRegex implements RegexCollectionInterface
{
    public function getPattern(): string
    {
        /**
         * @todo
         * @note return a regex pattern to detect a specific piece of sensitive data.
         */
        return 'something';
    }

    public function getTestableString(): string
    {
        /**
         * @todo
         * @note return a string that can be used to verify the regex pattern provided.
         */
        return 'something';
    }

    public function isSecret(): bool
    {
        return false;
    }
}
