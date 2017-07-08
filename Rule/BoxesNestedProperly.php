<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

/**
 * The relevant page boxes – namely MediaBox, BleedBox and TrimBox – must be
 * nested properly. The TrimBox must extend neither beyond the BleedBox nor the
 * MediaBox, and the BleedBox must not extend beyond the MediaBox.
 */
class BoxesNestedProperly extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($document->getPages() as $page) {
            $details = $page->getDetails();

            if (!array_key_exists('TrimBox', $details)
                || !array_key_exists('BleedBox', $details)
                || !array_key_exists('MediaBox', $details)
            ) {
                continue;
            }

            $trimBox = $details['TrimBox'];
            $bleedBox = $details['BleedBox'];
            $mediaBox = $details['MediaBox'];

            if (!$this->boxContainsBox($mediaBox, $trimBox)) {
                $violations->add($this->createViolation('The TrimBox must not extend beyond the MediaBox', $page));
            }

            if (!$this->boxContainsBox($bleedBox, $trimBox)) {
                $violations->add($this->createViolation('The TrimBox must not extend beyond the BleedBox', $page));
            }

            if (!$this->boxContainsBox($mediaBox, $bleedBox)) {
                $violations->add($this->createViolation('The BleedBox must not extend beyond the MediaBox', $page));
            }
        }
    }

    private function boxContainsBox($outer, $inner)
    {
        return $outer[0] <= $inner[0] && $outer[1] <= $inner[1] && $outer[2] >= $inner[2] && $outer[3] >= $inner[3];
    }
}
