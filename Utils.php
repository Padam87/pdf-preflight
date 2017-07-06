<?php

namespace Padam87\PdfPreflight;

use Smalot\PdfParser\XObject\Image;

class Utils
{
    public static function imageToImagick(Image $image): \Imagick
    {
        $details = $image->getDetails();

        switch ($details['ColorSpace']) {
            case 'DeviceRGB':
                $format = 'RGB';

                break;
            case 'DeviceGray':
                $format = 'GRAY';

                break;
            case 'DeviceCMYK':
            default:
                $format = 'CMYK';

                break;
        }

        $img = new \Imagick();
        $img->setFormat($format);
        $img->setSize($details['Width'], $details['Height']);
        $img->setOption('depth', $details['BitsPerComponent']);
        $img->readImageBlob($image->getContent());

        return $img;
    }

    public static function getInkDensity($c, $m, $y, $k): float
    {
        return (float) (($c + $m + $y + $k) * 100);
    }

    public static function rgbToCmyk($r, $g, $b): array
    {
        $c = (255 - $r) / 255;
        $m = (255 - $g) / 255;
        $y = (255 - $b) / 255;

        $k = min([$c, $m, $y]);
        $c = $c - $k;
        $m = $m - $k;
        $y = $y - $k;

        return [$c, $m, $y, $k];
    }
}
