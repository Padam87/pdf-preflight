<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

class PageCount extends AbstractRule
{
    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function doValidate(Document $document, Violations $violations)
    {
        $count = count($document->getPages());

        if ($count < $this->min || $count > $this->max) {
            if ($this->min === $this->max) {
                $message = sprintf('The page count must be %d, but the document has %d pages.', $this->min, $count);
            } else {
                $message = sprintf(
                    'The page count must be between %d and %d, but the document has %d pages.',
                    $this->min,
                    $this->max,
                    $count
                );
            }

            $violations->add($this->createViolation($message));
        }
    }
}
