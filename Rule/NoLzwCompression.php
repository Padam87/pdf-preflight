<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

/**
 * Untested, as I have no pdf that uses LZW.
 */
class NoLzwCompression implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($document->getPages() as $page) {
            $filter = $page->getDetails()['Contents']['Filter'];

            if ($this->isLzw($filter)) {
                $errors[] = [
                    'message' => 'LZW compression used in page.',
                    'object' => $page,
                ];
            }
        }

        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $image) {
            $filter = $image->getDetails()['Filter'];

            if ($this->isLzw($filter)) {
                $errors[] = [
                    'message' => 'LZW compression used in image.',
                    'object' => $image,
                ];
            }
        }

        return $errors;
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
