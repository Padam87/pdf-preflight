<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class InfoKeysExist implements RuleInterface
{
    /**
     * @var array
     */
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($this->keys as $key) {
            if (!array_key_exists($key, $document->getDetails())) {
                $errors = [
                    'message' => sprintf('The key "%s" is required, but not found in the info dict.', $key)
                ];
            }
        }

        return $errors;
    }
}
