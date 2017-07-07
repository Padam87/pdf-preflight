<?php

namespace Padam87\PdfPreflight\Standard;

use Padam87\PdfPreflight\Rule\RuleInterface;
use Smalot\PdfParser\Document;

interface StandardInterface
{
    /**
     * @return RuleInterface[]
     */
    public function getRules(): array;

    public function validate(Document $document): array;
}
