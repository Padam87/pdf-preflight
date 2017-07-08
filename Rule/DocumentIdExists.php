<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Header;

/**
 * Document ID must be present in PDF trailer
 */
class DocumentIdExists extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        $refl = new \ReflectionProperty(Document::class, 'trailer');
        $refl->setAccessible(true);

        /** @var Header $trailer */
        $trailer = $refl->getValue($document);

        if (!$trailer->has('Id')) {
            $violations->add($this->createViolation('Document ID must be present in PDF trailer.'));
        }
    }
}
