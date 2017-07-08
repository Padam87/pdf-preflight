<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class InfoSpecifiesTrapped extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        if (!array_key_exists('Trapped', $document->getDetails())) {
            $violations->add($this->createViolation('The info dict does not specify Trapped'));

            return;
        }

        $trapped = $document->getDetails()['Trapped'];

        if (!in_array($trapped, ['True', 'False'])) {
            $violations->add(
                $this->createViolation(
                    sprintf(
                        'The info dict specifies Trapped, but its value is invalid. Must be True or False, %s given',
                        $trapped
                    )
                )
            );
        }
    }
}
