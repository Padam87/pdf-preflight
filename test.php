<?php

namespace Padam87\PdfPreflight;

include 'vendor/autoload.php';

use Padam87\PdfPreflight\Rule\InfoKeysExist;
use Padam87\PdfPreflight\Rule\PageCount;
use Padam87\PdfPreflight\Standard\Printmagus;
use Smalot\PdfParser\Object as XObject;
use Smalot\PdfParser\Parser;
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

$parser = new Parser();
$document = $parser->parseFile('./gls.pdf');
//$document = $parser->parseFile('./test.pdf');
//$document = $parser->parseFile('./hotel.pdf');
//$document = $parser->parseFile('./js.pdf');


$preflight = new Preflight();
$preflight
    ->addStandard(new Printmagus())
    ->addRule(new PageCount(10, 15))
;

$violations = $preflight->validate($document);

dump($violations);
dump($violations->getViolationsForRule(InfoKeysExist::class));

$pageViolations = [];
foreach ($document->getPages() as $k => $page) {
    $pageViolations[$k] = $violations->getViolationsForPage($page)->count();
}
dump($pageViolations, array_sum($pageViolations));
dump($violations->getViolationsForDocument());

