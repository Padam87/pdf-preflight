<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class NoSeparation implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($document->getPages() as $page) {
            if (array_key_exists('SeparationInfo', $page->getDetails())) {
                $errors[] = [
                    'message' => 'Separated page found.',
                    'object' => $page,
                ];
            }
        }

        return $errors;
    }
}
