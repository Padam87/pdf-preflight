<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

class InfoKeysExist extends AbstractRule
{
    /**
     * @var array
     */
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($this->keys as $key) {
            if (!array_key_exists($key, $document->getDetails())) {
                $violations->add(
                    $this->createViolation(sprintf('The key "%s" is required, but not found in the info dict.', $key))
                );
            }
        }
    }
}
