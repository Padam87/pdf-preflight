<?php

namespace Padam87\PdfPreflight\Standard;

use Padam87\PdfPreflight\PreflightInterface;
use Padam87\PdfPreflight\Rule\RuleInterface;

interface StandardInterface extends PreflightInterface
{
    /**
     * @return RuleInterface[]
     */
    public function getRules(): array;
}
