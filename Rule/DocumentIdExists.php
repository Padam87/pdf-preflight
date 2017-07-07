<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Header;
use Smalot\PdfParser\XObject\Image;

/**
 * Document ID must be present in PDF trailer
 */
class DocumentIdExists implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        $refl = new \ReflectionProperty(Document::class, 'trailer');
        $refl->setAccessible(true);

        /** @var Header $trailer */
        $trailer = $refl->getValue($document);

        if (!$trailer->has('Id')) {
            $errors[] = [
                'message' => 'Document ID must be present in PDF trailer.'
            ];
        }

        return $errors;
    }
}
