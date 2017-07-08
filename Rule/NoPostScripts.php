<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

class NoPostScripts extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        /** @var PdfObject $object */
        foreach ($document->getObjectsByType('XObject', 'PS') as $k => $object) {
            $violations->add($this->createViolation('Embedded PostScript found.', $object));
        }
    }
}
