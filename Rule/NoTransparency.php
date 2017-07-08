<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Form;
use Smalot\PdfParser\XObject\Image;

/**
 * Transparency not allowed
 *
 * Untested
 */
class NoTransparency extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        /**
         * TODO: The specification only gives a transparency example with forms... what about images?
         *
         * @var Form $form
         */
        foreach ($document->getObjectsByType('XObject', 'Form') as $form) {
            if ($form->getHeader()->has('Group')) {
                $group = $form->getHeader()->get('Group');

                if (array_key_exists('S', $group) && $group['S'] === 'Transparency') {
                    $violations->add($this->createViolation('Transparent Form detected.', $form));
                }
            }
        }
    }
}
