<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class InfoSpecifiesTrapped implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        if (!array_key_exists('Trapped', $document->getDetails())) {
            $errors[] = [
                'message' => 'The info dict does not specify Trapped'
            ];

            return $errors;
        }

        $trapped = $document->getDetails()['Trapped'];

        if (!in_array($trapped, ['True', 'False'])) {
            $errors[] = [
                'message' => sprintf(
                    'The info dict specifies Trapped, but its value is invalid. Must be True or False, %s given',
                    $trapped
                )
            ];
        }

        return $errors;
    }
}
