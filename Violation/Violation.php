<?php

namespace Padam87\PdfPreflight\Violation;

use Padam87\PdfPreflight\Rule\RuleInterface;
use Smalot\PdfParser\Object as PdfObject;
use Smalot\PdfParser\Page;

class Violation
{
    private $rule;
    private $ruleFqcn;
    private $message;
    private $object;
    private $page;
    private $extra;

    public function __construct(
        RuleInterface $rule,
        string $message,
        PdfObject $object = null,
        Page $page = null,
        array $extra = []
    ) {
        $this->rule = $rule;
        $this->ruleFqcn = get_class($rule);
        $this->message = $message;
        $this->page = $page;
        $this->object = $object;
        $this->extra = $extra;
    }

    public function getRuleFqcn(): string
    {
        return $this->ruleFqcn;
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return PdfObject|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return Page|null
     */
    public function getPage()
    {
        return $this->page;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}
