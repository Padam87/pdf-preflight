<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Utils;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class MaxInkDensityImage implements RuleInterface
{
    /**
     * @var int
     */
    private $limit;

    public function __construct(int $limit = 300)
    {
        $this->limit = $limit;
    }

    public function validate(Document $document) : array
    {
        $errors = [];

        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $k => $image) {
            $img = Utils::imageToImagick($image);

            if ($img->getColorspace() != \Imagick::COLORSPACE_CMYK) {
                $img->transformImageColorspace(\Imagick::COLORSPACE_CMYK);
            }

            // $img->getImageTotalInkDensity() returns a totally wrong value, no idea what would that mean

            $identity = $img->identifyImage(true);
            preg_match('/Total ink density: ([0-9]*(.[0-9]*)?)%/', $identity['rawOutput'], $matches);

            $dens = $matches[1];

            if ($dens > $this->limit) {
                $errors[] = [
                    'message' => 'Max ink density limit exceeded.',
                    'object' => $image,
                    'density' => $dens,
                ];
            }
        }

        return $errors;
    }
}
