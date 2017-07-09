<?php

namespace Padam87\PdfPreflight;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

interface PreflightInterface
{
    public function validate(Document $document) : Violations;

    /**
     * Some rules depend on streams.
     * These streams have to be decoded before use, which takes considerable time.
     *
     * When you include a rule like this, expect a moderate slowdown.
     *
     * @return bool
     */
    public function isDependentOnStreams() : bool;
}
