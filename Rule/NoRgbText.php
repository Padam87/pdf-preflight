<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as XObject;
use Smalot\PdfParser\Page;

class NoRgbText extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        $page = null;

        /** @var XObject $object */
        foreach ($document->getObjects() as $k => $object) {
            $error = null;

            if (get_class($object) == Page::class) {
                $page = $object;
            }

            if (get_class($object) != XObject::class) {
                continue;
            }

            if (empty(trim($object->getText()))) {
                continue;
            }

            $sections = $object->getSectionsText($object->getContent());

            foreach ($sections as $section) {
                $commands = $object->getCommandsText($section);
                $norm = [];

                foreach ($commands as $command) {
                    $norm[$command[XObject::OPERATOR]] = $command[XObject::COMMAND];
                }

                $valid = $this->validateColors($norm);

                if (isset($norm['CS'])) { // stroking color space
                    $valid = $valid && $this->validateColorSpace($norm['CS'], $page);
                }

                if (isset($norm['cs'])) { // nonstroking color space
                    $valid = $valid && $this->validateColorSpace($norm['cs'], $page);
                }

                if (!$valid) {
                    $violations->add(
                        $this->createViolation(
                            'Text with RGB colors.',
                            $object,
                            $page,
                            ['rgb' => $norm['RG'] ?? $norm['rg']]
                        )
                    );
                }
            }
        }
    }

    private function validateColorSpace($colorSpace, Page $page)
    {
        if ($colorSpace === null) {
            return true;
        }

        // These are direct definitions
        if (in_array($colorSpace, ['DeviceGray', 'DeviceRGB', 'DeviceCMYK', 'Pattern'])) {
            if ($colorSpace === 'DeviceRGB') {
                return false;
            }
        } else { // The color space is not defined directly, it is a reference, we have to look it up
            if ($page->getHeader()->has('Resources') && $page->getHeader()->get('Resources')->has('ColorSpace')) {
                $colorSpaces = $page->getHeader()->get('Resources')->get('ColorSpace')->getDetails();

                if (isset($colorSpaces[$colorSpace])) {
                    $definition = $colorSpaces[$colorSpace];

                    foreach ($definition as $cs) {
                        if ($cs === 'DeviceRGB') {
                            return false;
                        }
                    }
                } else {
                    throw new \LogicException(
                        sprintf(
                            'The object refers to the "%s" color space, but the page only defines: %s',
                            $colorSpace,
                            implode(', ', array_keys($colorSpaces))
                        )
                    );
                }
            } else {
                throw new \LogicException(
                    sprintf(
                        'The object refers to the "%s" color space, but the page defines no color spaces',
                        $colorSpace
                    )
                );
            }
        }

        return true;
    }

    private function validateColors($norm)
    {
        if (isset($norm['RG']) || isset($norm['rg'])) {
            return false;
        }

        return true;
    }
}
