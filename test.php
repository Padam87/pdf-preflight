<?php

namespace Padam87\PdfPreflight;

include 'vendor/autoload.php';

use Padam87\PdfPreflight\Rule\MaxInkDensityImage;
use Padam87\PdfPreflight\Rule\MaxInkDensityText;
use Padam87\PdfPreflight\Rule\NoRgbImages;
use Padam87\PdfPreflight\Rule\ImageMinDpi;
use Padam87\PdfPreflight\Rule\NoRgbText;
use Padam87\PdfPreflight\Rule\RuleInterface;
use Smalot\PdfParser\Object as XObject;
use Smalot\PdfParser\Parser;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

VarDumper::setHandler(function ($var) {
    $cloner = new VarCloner(
        [
            XObject::class => function (XObject $object, $array, Stub $stub, $isNested, $filter) {
                return [
                    'details' => $object->getDetails(),
                    'text' => $object->getText(),
                ];
            },
        ]
    );
    $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();

    $dumper->dump($cloner->cloneVar($var));
});

/** @var RuleInterface[] $rules */
$rules = [
    new ImageMinDpi(),
    new NoRgbImages(),
    new NoRgbText(),
    new MaxInkDensityImage(300),
    new MaxInkDensityText(100),
];

$stopwatch = new Stopwatch();
$stopwatch->start('parse');

$parser = new Parser();
//$document = $parser->parseFile('./gls.pdf');
$document = $parser->parseFile('./test.pdf');

$event = $stopwatch->stop('parse');

dump($event->getDuration());

$stopwatch->start('preflight');

foreach ($rules as $rule) {
    $errors = $rule->validate($document);

    $event = $stopwatch->lap('preflight');
    $periods = $event->getPeriods();
    $period = end($periods);

    dump(
        [
            'rule' => get_class($rule),
            'errors' => $errors,
            'duration' => $period->getDuration(),
        ]
    );
}

$event = $stopwatch->stop('preflight');

dump($event->getDuration());

