<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class ImageMinDpi implements RuleInterface
{
    /**
     * @var int
     */
    private $minDpi;

    public function __construct(int $minDpi = 300)
    {
        $this->minDpi = $minDpi;
    }

    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($document->getPages() as $page) {
            $mediaBox = $page->get('MediaBox')->getDetails();

            $w = $mediaBox[2] / 72;
            $h = $mediaBox[3] / 72;

            foreach ($page->getXObjects() as $id => $object) {
                if (!is_int($id)) { // the parser returns images with both string and int keys, resulting in duplicates
                    continue;
                }

                if (!$object instanceof Image) {
                    continue;
                }

                $details = $object->getHeader()->getDetails();

                $dpi = [
                    'horizontal' => $details['Width'] / $w,
                    'vertical' => $details['Height'] / $h,
                ];

                if ($dpi['horizontal'] < $this->minDpi || $dpi['vertical'] < $this->minDpi) {
                    $errors[] = [
                        'message' => 'Image with too low DPI',
                        'object' => $object,
                        'dpi' => $dpi,
                    ];
                }
            }
        }

        return $errors;
    }
}
