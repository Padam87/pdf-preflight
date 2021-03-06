<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Utils;
use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

/**
 * This can be extremely slow when a PDF contains a lot of large images.
 * (Imagick identifyImage method is very slow.)
 *
 * This rule also requires decoded streams, further slowing down the process.
 *
 * Avoid unless absolutely necessary.
 */
class MaxInkDensityImage extends AbstractRule
{
    /**
     * @var int
     */
    private $limit;

    public function __construct(int $limit = 300)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function isDependentOnStreams() : bool
    {
        return true;
    }

    public function doValidate(Document $document, Violations $violations)
    {
        /** @var Image $image */
        foreach ($document->getObjectsByType('XObject', 'Image') as $k => $image) {
            $img = Utils::imageToImagick($image);

            if ($img->getColorspace() != \Imagick::COLORSPACE_CMYK) {
                $img->transformImageColorspace(\Imagick::COLORSPACE_CMYK);
            }

            $dens = $this->getInkDensity($img);

            if ($dens > $this->limit) {
                $violations->add(
                    $this->createViolation('Max ink density limit exceeded.', $image, null, ['density' => $dens])
                );
            }
        }
    }

    private function getInkDensity(\Imagick $img)
    {
        $identity = $img->identifyImage(true);

        preg_match('/Total ink density: ([0-9]*(.[0-9]*)?)%/', $identity['rawOutput'], $matches);

        return $matches[1];
    }
}
