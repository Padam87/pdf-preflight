<?php

namespace Padam87\PdfPreflight;

use Padam87\PdfPreflight\Rule\RuleInterface;
use Padam87\PdfPreflight\Standard\StandardInterface;
use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

class Preflight implements PreflightInterface
{
    /**
     * @var StandardInterface[]
     */
    private $standards = [];

    /**
     * @var RuleInterface[]
     */
    private $rules = [];

    public function validate(Document $document): Violations
    {
        $violations = new Violations();

        foreach ($this->getStandards() as $standard) {
            $violations->merge($standard->validate($document));
        }

        foreach ($this->getRules() as $rule) {
            $violations->merge($rule->validate($document));
        }

        return $violations;
    }

    /**
     * @return StandardInterface[]
     */
    public function getStandards(): array
    {
        return $this->standards;
    }

    public function addStandard(StandardInterface $standard): Preflight
    {
        $this->standards[] = $standard;

        return $this;
    }

    /**
     * @return RuleInterface[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRule(RuleInterface $rule): Preflight
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDependentOnStreams() : bool
    {
        foreach ($this->getStandards() as $standard) {
            if ($standard->isDependentOnStreams()) {
                return true;
            }
        }

        foreach ($this->getRules() as $rule) {
            if ($rule->isDependentOnStreams()) {
                return true;
            }
        }

        return false;
    }
}
