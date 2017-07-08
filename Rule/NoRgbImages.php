<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class NoRgbImages extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $image) {
            if ($image->get('ColorSpace') == 'DeviceRGB') {
                $violations->add($this->createViolation('RGB image detected.', $image));
            }
        }
    }
}
