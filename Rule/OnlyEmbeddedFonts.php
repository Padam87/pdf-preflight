<?php

namespace Padam87\PdfPreflight\Rule;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Font;
use Smalot\PdfParser\XObject\Image;

class OnlyEmbeddedFonts implements RuleInterface
{
    public function validate(Document $document) : array
    {
        $errors = [];

        $fonts = $document->getDictionary()['Font'];

        foreach ($fonts as $id) {
            /** @var Font $font */
            $font = $document->getObjectById($id);

            $embedded = $this->validateFont($font, $document);

            if (!$embedded) {
                $errors[] = [
                    'message' => 'Font not embedded.',
                    'object' => $font
                ];
            }
        }

        return $errors;
    }

    private function validateFont(Font $font, Document $document)
    {
        $details = $font->getDetails();

        // A font that defines glyphs with streams of PDF graphics operators (embedded by default)
        if ($details['Subtype'] === 'Type3') {
            return true;
        }

        // A composite font—a font composed of glyphs from a descendant CIDFont
        if ($details['Subtype'] === 'Type0') {
            foreach($details['DescendantFonts'] as $subId) {
                /** @var Font $font */
                $font = $document->getObjectById(str_replace('#Obj#', '', $subId));

                if (!$this->validateFont($font, $document)) {
                    return false;
                }
            }

            return true;
        }

        if (isset($details['FontDescriptor'])) {
            $descriptor = $this->getDescriptor($details['FontDescriptor'], $document);

            if (isset($descriptor['FontFile']) || isset($descriptor['FontFile2']) || isset($descriptor['FontFile3'])) {
                return true;
            }

            return false;
        }

        return false;
    }

    private function getDescriptor(array $descriptor, Document $document)
    {
        foreach ($document->getDictionary()['FontDescriptor'] as $id) {
            $object = $document->getObjectById($id);
            $details = $object->getDetails();

            if ($details['FontName'] === $descriptor['FontName']) {
                return $details;
            }
        }

        return null;
    }
}
