<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

/**
 * Untested, as I have no pdf that uses LZW.
 */
class NoLzwCompression extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($document->getPages() as $page) {
            $filter = $page->getDetails()['Contents']['Filter'];

            if ($this->isLzw($filter)) {
                $violations->add($this->createViolation('LZW compression used in page.', $page));
            }
        }

        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $image) {
            $filter = $image->getDetails()['Filter'];

            if ($this->isLzw($filter)) {
                $violations->add($this->createViolation('LZW compression used in image.', $image));
            }
        }
    }

    private function isLzw($filter)
    {
        if (is_array($filter)) {
            $lzw = in_array('LZWDecode', $filter);
        } else {
            $lzw = $filter == 'LZWDecode';
        }

        return $lzw;
    }
}
