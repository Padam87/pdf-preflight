<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

class NoActions implements RuleInterface
{
    CONST ACTIONS = [
        'GoTo',
        'GoToR',
        'GoToE',
        'Launch',
        'Thread',
        'URI',
        'Sound',
        'Movie',
        'Hide',
        'Named',
        'SubmitForm',
        'ResetForm',
        'ImportData',
        'JavaScript',
        'SetOCGState',
        'Rendition',
        'Trans',
        'GoTo3DView',
    ];

    public function validate(Document $document) : array
    {
        $errors = [];

        /** @var PdfObject $object */
        foreach ($document->getObjects() as $k => $object) {
            if ($object->getHeader()->has('S')
                && in_array($object->getHeader()->get('S'), self::ACTIONS)) {
                $errors[] = [
                    'message' => sprintf('%s action found', $object->getHeader()->get('S')),
                    'object' => $object,
                ];
            }
        }

        return $errors;
    }
}
