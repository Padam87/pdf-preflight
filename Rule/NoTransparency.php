<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Form;
use Smalot\PdfParser\XObject\Image;

/**
 * Transparency not allowed
 *
 * Untested
 */
class NoTransparency implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        /**
         * TODO: The specification only gives a transparency example with forms... what about images?
         *
         * @var Form $form
         */
        foreach ($document->getObjectsByType('XObject', 'Form') as $form) {
            if ($form->getHeader()->has('Group')) {
                $group = $form->getHeader()->get('Group');

                if (array_key_exists('S', $group) && $group['S'] === 'Transparency') {
                    $errors[] = [
                        'message' => 'Transparent Form detected.',
                        'object' => $form,
                    ];
                }
            }
        }

        return $errors;
    }
}
