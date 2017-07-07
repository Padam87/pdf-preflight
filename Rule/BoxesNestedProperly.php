<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;

/**
 * The relevant page boxes – namely MediaBox, BleedBox and TrimBox – must be
 * nested properly. The TrimBox must extend neither beyond the BleedBox nor the
 * MediaBox, and the BleedBox must not extend beyond the MediaBox.
 */
class BoxesNestedProperly implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($document->getPages() as $page) {
            $details = $page->getDetails();

            if (!array_key_exists('TrimBox', $details)
                || !array_key_exists('BleedBox', $details)
                || !array_key_exists('MediaBox', $details)) {
                continue;
            }

            $trimBox = $details['TrimBox'];
            $bleedBox = $details['BleedBox'];
            $mediaBox = $details['MediaBox'];

            if (!$this->boxContainsBox($mediaBox, $trimBox)) {
                $errors[] = [
                    'message' => 'The TrimBox must not extend beyond the MediaBox',
                    'object' => $page,
                ];
            }

            if (!$this->boxContainsBox($bleedBox, $trimBox)) {
                $errors[] = [
                    'message' => 'The TrimBox must not extend beyond the BleedBox',
                    'object' => $page,
                ];
            }

            if (!$this->boxContainsBox($mediaBox, $bleedBox)) {
                $errors[] = [
                    'message' => 'The BleedBox must not extend beyond the MediaBox',
                    'object' => $page,
                ];
            }
        }

        return $errors;
    }

    private function boxContainsBox($outer, $inner)
    {
        return $outer[0] <= $inner[0]
            && $outer[1] <= $inner[1]
            && $outer[2] >= $inner[2]
            && $outer[3] >= $inner[3]
        ;
    }
}
