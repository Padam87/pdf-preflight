<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

class NoActions extends AbstractRule
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

    public function doValidate(Document $document, Violations $violations)
    {
        /** @var PdfObject $object */
        foreach ($document->getObjects() as $k => $object) {
            if ($object->getHeader()->has('S') && in_array($object->getHeader()->get('S'), self::ACTIONS)) {
                $violations->add(
                    $this->createViolation(sprintf('%s action found', $object->getHeader()->get('S')), $object)
                );
            }
        }
    }
}
