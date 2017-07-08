<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

class HasOutputIntent extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($document->getDictionary()['Catalog'] as $id) {
            $meta = $document->getObjectById($id);
            $details = $meta->getDetails();

            if (array_key_exists('OutputIntents', $details)) {
                return null;
            }
        }

        $violations->add($this->createViolation('OutputIntent must be present'));
    }
}
