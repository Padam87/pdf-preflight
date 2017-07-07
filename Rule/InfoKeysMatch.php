<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\XObject\Image;

class InfoKeysMatch implements RuleInterface
{
    /**
     * @var array
     */
    private $expressions;

    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    public function validate(Document $document) : array
    {
        $errors = [];

        foreach ($this->expressions as $key => $expression) {
            if (!array_key_exists($key, $document->getDetails())) {
                $errors = [
                    'message' => sprintf('The key "%s" is required, but not found in the info dict.', $key)
                ];
            }

            $value = $document->getDetails()[$key];

            if (false == preg_match($expression, $value)) {
                $errors = [
                    'message' => sprintf(
                        'The key "%s" must match the expression "%s", contains "%s"',
                        $key,
                        $expression,
                        $value
                    ),
                ];
            }
        }

        return $errors;
    }
}
