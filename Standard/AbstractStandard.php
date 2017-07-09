<?php

namespace Padam87\PdfPreflight\Standard;

abstract class AbstractStandard implements StandardInterface
{
    public function isDependentOnStreams() : bool
    {
        foreach ($this->getRules() as $rule) {
            if ($rule->isDependentOnStreams()) {
                return true;
            }
        }

        return false;
    }
}
