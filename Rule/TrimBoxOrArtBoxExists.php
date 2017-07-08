<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Header;
use Smalot\PdfParser\XObject\Image;

/**
 * A PDF/X file must contain either a TrimBox or an ArtBox for every page in the PDF.
 * While both TrimBox or ArtBox may be used, the PDF/X-3 standard recommends to
 * prefer the TrimBox.
 */
class TrimBoxOrArtBoxExists extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($document->getPages() as $page) {
            $details = $page->getDetails();

            if (!array_key_exists('TrimBox', $details) && !array_key_exists('ArtBox', $details)) {
                $violations->add($this->createViolation('A page without a TrimBox or an ArtBox found.', $page));
            }
        }
    }
}
