<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;

interface RuleInterface
{
    public function validate(Document $document) : array;
}
