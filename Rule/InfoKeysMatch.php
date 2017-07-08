<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class InfoKeysMatch extends AbstractRule
{
    /**
     * @var array
     */
    private $expressions;

    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    public function doValidate(Document $document, Violations $violations)
    {
        foreach ($this->expressions as $key => $expression) {
            if (!array_key_exists($key, $document->getDetails())) {
                $violations->add(
                    $this->createViolation(sprintf('The key "%s" is required, but not found in the info dict.', $key))
                );

                continue;
            }

            $value = $document->getDetails()[$key];

            if (false == preg_match($expression, $value)) {
                $violations->add(
                    $this->createViolation(
                        sprintf(
                            'The key "%s" must match the expression "%s", contains "%s"',
                            $key,
                            $expression,
                            $value
                        )
                    )
                );
            }
        }
    }
}
