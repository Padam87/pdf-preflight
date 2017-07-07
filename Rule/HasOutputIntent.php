<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;

class HasOutputIntent implements RuleInterface
{
    public function validate(Document $document) : array
    {
        foreach ($document->getDictionary()['Catalog'] as $id)
        {
            $meta = $document->getObjectById($id);
            $details = $meta->getDetails();

            if (array_key_exists('OutputIntents', $details)) {
                return [];
            }
        }

        return [
            [
                'message' => 'OutputIntent must be present',
            ]
        ];
    }
}
