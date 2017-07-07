<?php

namespace Padam87\PdfPreflight\Standard;

use Padam87\PdfPreflight\Rule\InfoKeysExist;
use Padam87\PdfPreflight\Rule\NoRgbImages;
use Padam87\PdfPreflight\Rule\NoRgbText;
use Smalot\PdfParser\Document;

/**
 * The PDF/X-1a standard
 *
 * - [ ] PDF must be version 1.3 or earlier
 * - [ ] Page must not be separated
 * - [ ] OutputIntent must be present
 * - [ ] OutputIntent must contain exactly one PDF/X entry
 * - [ ] OutputConditionIdentifier required in PDF/X OutputIntent
 * - [ ] Destination profile must be embedded or Registry Name must be filled out
 * - [ ] OutputIntent Info key must be present
 * - [ ] Destination profile must be ICC output profile (type ‘prtr’)
 * - [x] Only DeviceCMYK and spot colors allowed
 * - [ ] Fonts must be embedded
 * - [ ] LZW compression prohibited
 * - [ ] Trapped key must be True or False
 * - [x] GTS_PDFXVersion key must be present
 * - [ ] Invalid GTS_PDFXVersion (PDF/X-1a)
 * - [ ] Invalid GTS_PDFXConformance (PDF/X-1a)
 * - [x] CreationDate, CreationDate and Title required
 * - [ ] Document ID must be present in PDF trailer
 * - [ ] Either TrimBox or ArtBox must be present
 * - [ ] Page boxes must be nested properly
 * - [ ] Transfer curves prohibited
 * - [ ] Halftone must be of Type 1 or 5
 * - [ ] Halftone Name key prohibited
 * - [ ] Embedded PostScript prohibited
 * - [ ] Encryption prohibited
 * - [ ] Alternate image must not be default for printing
 * - [ ] Annotation and Acrobat form elements must be outside of TrimBox and BleedBox
 * - [ ] Actions and JavaScript prohibited
 * - [ ] Operators not defined in PDF 1.3 prohibited
 * - [ ] File specifications not allowed
 * - [ ] Transparency not allowed
 */
class X1a implements StandardInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return [
            new InfoKeysExist(['Title', 'CreationDate', 'CreationDate', 'GTS_PDFXVersion']),
            new NoRgbImages(),
            new NoRgbText(),
        ];
    }

    public function validate(Document $document): array
    {
        $errors = [];

        foreach ($this->getRules() as $rule) {
            $errors[get_class($rule)] = $rule->validate($document);
        }

        return $errors;
    }
}
