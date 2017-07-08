<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violation;
use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;
use Smalot\PdfParser\Page;

abstract class AbstractRule implements RuleInterface
{
    /**
     * @var Document
     */
    private $document;

    abstract public function doValidate(Document $document, Violations $violations);

    public function validate(Document $document) : Violations
    {
        $violations = new Violations();

        $this->document = $document;
        $this->doValidate($document, $violations);
        $this->document = null;

        return $violations;
    }

    protected function createViolation(
        string $message,
        PdfObject $object = null,
        Page $page = null,
        array $extra = []
    ): Violation
    {
        if (get_class($object) == Page::class) {
            $page = $object;
        }

        if ($object && !$page) {
            $page = $this->findPageForObject($object);
        }

        return new Violation($this, $message, $object, $page, $extra);
    }

    protected function findPageForObject(PdfObject $object)
    {
        $document = $this->document;
        $objects = $document->getObjects();

        $key = array_search($object, $objects);
        $index = array_search($key, array_keys($objects));
        $objects = array_reverse(array_slice($objects, 0, $index));

        foreach ($objects as $obj) {
            if (get_class($obj) == Page::class) {
                return $obj;
            }
        }

        return null;
    }
}
