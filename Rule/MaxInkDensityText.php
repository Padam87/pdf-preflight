<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Utils;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as XObject;

class MaxInkDensityText implements RuleInterface
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

        /** @var XObject $object */
        foreach ($document->getObjects() as $object) {
            if (get_class($object) != XObject::class) {
                continue;
            }

            $max = 0;
            $sections = $object->getSectionsText($object->getContent());

            foreach ($sections as $section) {
                $commands = $object->getCommandsText($section);
                $norm = [];

                foreach ($commands as $command) {
                    $norm[$command[XObject::OPERATOR]] = $command[XObject::COMMAND];
                }

                if (isset($norm['RG'])) { // RGB stroking
                    list($r, $g, $b) = explode(' ', $norm['RG']);
                    list($c, $m, $y, $k) = Utils::rgbToCmyk($r, $g, $b);

                    if ($max < $dens = Utils::getInkDensity($c, $m, $y, $k)) {
                        $max = $dens;
                    }
                }

                if (isset($norm['rg'])) { // RGB nonstroking
                    list($r, $g, $b) = explode(' ', $norm['rg']);
                    list($c, $m, $y, $k) = Utils::rgbToCmyk($r, $g, $b);

                    if ($max < $dens = Utils::getInkDensity($c, $m, $y, $k)) {
                        $max = $dens;
                    }
                }

                if (isset($norm['K'])) { // CMYK stroking
                    list($c, $m, $y, $k) = explode(' ', $norm['K']);

                    if ($max < $dens = Utils::getInkDensity($c, $m, $y, $k)) {
                        $max = $dens;
                    }
                }

                if (isset($norm['k'])) { // CMYK nonstroking
                    list($c, $m, $y, $k) = explode(' ', $norm['k']);

                    if ($max < $dens = Utils::getInkDensity($c, $m, $y, $k)) {
                        $max = $dens;
                    }
                }
            }

            if ($max > $this->limit) {
                $errors[] = [
                    'message' => 'Max ink density limit exceeded.',
                    'object' => $object,
                    'density' => $max,
                ];
            }
        }

        return $errors;
    }
}
