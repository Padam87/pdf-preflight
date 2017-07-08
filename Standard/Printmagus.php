<?php

namespace Padam87\PdfPreflight\Standard;

use Padam87\PdfPreflight\Rule\ImageMinDpi;
use Padam87\PdfPreflight\Rule\MaxInkDensityImage;
use Padam87\PdfPreflight\Rule\MaxInkDensityText;

/**
 * The standard used @ printmagus.com
 */
class Printmagus extends X1a
{
    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        $rules = parent::getRules();

        return array_merge(
            $rules,
            [
                new ImageMinDpi(300),
                //new MaxInkDensityImage(320),
                new MaxInkDensityText(320),
            ]
        );
    }
}
