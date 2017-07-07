<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

class NoPostScripts implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        /** @var PdfObject $object */
        foreach ($document->getObjectsByType('XObject', 'PS') as $k => $object) {
            $errors[] = [
                'message' => 'Embedded PostScript found',
                'object' => $object,
            ];
        }

        return $errors;
    }
}
