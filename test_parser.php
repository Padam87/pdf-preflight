<?php

namespace Padam87\PdfPreflight;

include 'vendor/autoload.php';

use Symfony\Component\Stopwatch\Stopwatch;

// This file is for measuring the Parser against the original TCPDF one

foreach (['./gls.pdf', './js.pdf', './hotel.pdf', './test.pdf', './test2.pdf', './test2b.pdf', './test3.pdf'] as $file) {
    $s = new Stopwatch();
    $contents = file_get_contents($file);

    $s->start('original');
    $originalData = (new \TCPDF_PARSER($contents))->getParsedData();
    $original = $s->stop('original');

    $p = new \Padam87\PdfPreflight\Parser\Parser();

    $s->start('optimized');
    $optimizedData = $p->parse($contents);
    $optimized = $s->stop('optimized');

    $s->start('decoded');
    $decodedData = $p->parse($contents, false);
    $decoded = $s->stop('decoded');

    dump(
        [
            'file' => $file,
            'original' => $original->getDuration(),
            'optimized' => $optimized->getDuration(),
            'not_decoded' => $decoded->getDuration(),
            //'diff' => isSame($originalData[1], $optimizedData[1]),
        ]
    );

    assert(array_keys($originalData[1]) == array_keys($optimizedData[1]));
}

function isSame($a, $b, $keychain = [], $ta = null, $tb = null)
{
    foreach ($a as $k => $va) {
        $vb = $b[$k];
        $valid = @assert($va == $vb);

        if (!$valid) {
            $keychain[] = $k;

            if (is_array($va) && is_array($vb)) {
                return isSame($va, $vb, $keychain, $ta == null ? $va : $ta, $tb == null ? $vb : $tb);
            } else {
                return [
                    'keychain' => implode(' .. ', $keychain),
                    'original' => $va,
                    'optimized' => $vb,
                    'topOriginal' => $ta,
                    'topOptimized' => $tb,
                ];
            }
        }
    }

    return true;
}

