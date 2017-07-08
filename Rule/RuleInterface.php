<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

interface RuleInterface
{
    public function validate(Document $document) : Violations;
}
