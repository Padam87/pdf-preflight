<?php

namespace Padam87\PdfPreflight;

use Smalot\PdfParser\Object as PdfObject;
use Smalot\PdfParser\XObject\Image;

class Utils
{
    public static function imageToImagick(Image $image): \Imagick
    {
        $details = $image->getDetails();
        $cs = $image->getHeader()->get('ColorSpace');

        if ($cs instanceof PdfObject) {
            if ($cs->has(0) && $cs->get(0) == 'ICCBased') {
                /** @var PdfObject $iccProfile */
                $iccProfile = $cs->get(1);

                switch ($iccProfile->getDetails()['N']) {
                    case 1:
                        $cs = 'DeviceGray';

                        break;
                    case 3:
                        $cs = 'DeviceRGB';

                        break;
                    case 4:
                        $cs = 'DeviceCMYK';

                        break;
                }
            }
        } else {
            $cs = (string) $cs;
        }

        $img = new \Imagick();
        $img->setSize($details['Width'], $details['Height']);
        $img->setOption('depth', $details['BitsPerComponent']);

        switch ($cs) {
            case 'DeviceRGB':
                $img->setFormat('RGB');
                $img->setColorspace(\Imagick::COLORSPACE_RGB);

                break;
            case 'DeviceGray':
                $img->setFormat('GRAY');
                $img->setColorspace(\Imagick::COLORSPACE_GRAY);

                break;
            case 'DeviceCMYK':
            default:
                $img->setFormat('CMYK');
                $img->setColorspace(\Imagick::COLORSPACE_CMYK);

                break;
        }

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
