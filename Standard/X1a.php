<?php

namespace Padam87\PdfPreflight\Standard;

use Padam87\PdfPreflight\Rule\BoxesNestedProperly;
use Padam87\PdfPreflight\Rule\DocumentIdExists;
use Padam87\PdfPreflight\Rule\HasOutputIntent;
use Padam87\PdfPreflight\Rule\InfoKeysExist;
use Padam87\PdfPreflight\Rule\InfoKeysMatch;
use Padam87\PdfPreflight\Rule\InfoSpecifiesTrapped;
use Padam87\PdfPreflight\Rule\NoActions;
use Padam87\PdfPreflight\Rule\NoLzwCompression;
use Padam87\PdfPreflight\Rule\NoPostScripts;
use Padam87\PdfPreflight\Rule\NoRgbImages;
use Padam87\PdfPreflight\Rule\NoRgbText;
use Padam87\PdfPreflight\Rule\NoSeparation;
use Padam87\PdfPreflight\Rule\NoTransparency;
use Padam87\PdfPreflight\Rule\OnlyEmbeddedFonts;
use Padam87\PdfPreflight\Rule\OutputIntentPdfx;
use Padam87\PdfPreflight\Rule\TrimBoxOrArtBoxExists;
use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;

/**
 * The PDF/X-1a standard
 *
 * - [ ] PDF must be version 1.3 or earlier
 * - [x] Page must not be separated
 * - [x] OutputIntent must be present
 * - [x] OutputIntent must contain exactly one PDF/X entry
 * - [x] OutputConditionIdentifier required in PDF/X OutputIntent
 * - [x] OutputIntent Info key must be present
 * - [x] Destination profile must be embedded or Registry Name must be filled out
 * - [x] Destination profile must be ICC output profile (type ‘prtr’)
 * - [x] Only DeviceCMYK and spot colors allowed
 * - [x] Fonts must be embedded
 * - [x] LZW compression prohibited
 * - [x] Trapped key must be True or False
 * - [x] GTS_PDFXVersion key must be present
 * - [x] Invalid GTS_PDFXVersion (PDF/X-1)
 * - [x] Invalid GTS_PDFXConformance (PDF/X-1a)
 * - [x] CreationDate, CreationDate and Title required
 * - [x] Document ID must be present in PDF trailer
 * - [x] Either TrimBox or ArtBox must be present
 * - [x] Page boxes must be nested properly
 * - [ ] Transfer curves prohibited
 * - [ ] Halftone must be of Type 1 or 5
 * - [ ] Halftone Name key prohibited
 * - [x] Embedded PostScript prohibited
 * - [ ] Encryption prohibited
 * - [ ] Alternate image must not be default for printing
 * - [ ] Annotation and Acrobat form elements must be outside of TrimBox and BleedBox
 * - [x] Actions and JavaScript prohibited
 * - [ ] Operators not defined in PDF 1.3 prohibited
 * - [ ] File specifications not allowed
 * - [x] Transparency not allowed
 */
class X1a extends AbstractStandard
{
    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return [
            new NoSeparation(),
            new HasOutputIntent(),
            new OutputIntentPdfx(),
            new OnlyEmbeddedFonts(),
            new NoLzwCompression(),
            new InfoKeysExist(['Title', 'CreationDate', 'CreationDate', 'GTS_PDFXVersion']),
            new InfoKeysMatch(['GTS_PDFXVersion' => '/PDF\/X-1/', 'GTS_PDFXConformance' => '/PDF\/X-1a/']),
            new InfoSpecifiesTrapped(),
            new DocumentIdExists(),
            new TrimBoxOrArtBoxExists(),
            new BoxesNestedProperly(),
            new NoRgbImages(),
            new NoRgbText(),
            new NoPostScripts(),
            new NoActions(),
            new NoTransparency(),
        ];
    }

    public function validate(Document $document): Violations
    {
        $violations = new Violations();

        foreach ($this->getRules() as $rule) {
            $violations->merge($rule->validate($document));
        }

        return $violations;
    }
}
