<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class NoRgbImages implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $image) {
            if ($image->get('ColorSpace') == 'DeviceRGB') {
                $errors[] = [
                    'message' => 'RGB image detected',
                    'object' => $image,
                ];
            }
        }

        return $errors;
    }
}
