<?php

namespace Padam87\PdfPreflight\Violation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Page;

class Violations extends ArrayCollection
{
    public function merge(Violations $violations)
    {
        foreach ($violations as $violation) {
            $this->add($violation);
        }
    }

    public function getViolationsForRule(string $ruleFqcn)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("ruleFqcn", $ruleFqcn))
        ;

        return $this->matching($criteria);
    }

    public function getViolationsForPage(Page $page)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("page", $page))
        ;

        return $this->matching($criteria);
    }

    public function getViolationsForPageNo(int $page, Document $document)
    {
        return $this->getViolationsForPage($document->getPages()[$page]);
    }

    public function getViolationsForDocument()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("object", null))
        ;

        return $this->matching($criteria);
    }
}
