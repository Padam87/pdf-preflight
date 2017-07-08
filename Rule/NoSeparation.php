<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class NoSeparation extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($document->getPages() as $page) {
            if (array_key_exists('SeparationInfo', $page->getDetails())) {
                $violations->add($this->createViolation('Separated page found.', $page));
            }
        }
    }
}
