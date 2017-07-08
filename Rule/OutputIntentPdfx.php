<?php

namespace Padam87\PdfPreflight\Rule;

use Padam87\PdfPreflight\Violation\Violations;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Object as PdfObject;

/**
 * (1) While an OutputIntent array may contain more than one entry, exactly one of its
 * entries must have the Subtype «GTS_PDFX».
 *
 * (2) The PDF/X OutputIntent entry must have an OutputConditionIdentifier.
 *
 * (3) The Info key in a PDF/X OutputIntent is required.
 *
 * (4) It is required for a PDF/X-1a or PDF/X-3 file that either the RegistryName be present
 * or an ICC output profile that characterizes the intended printing condition is embedded in the OutputIntent.
 *
 * (5) The ICC profile embedded as a destination profile into a PDF/X-3 OutputIntent must
 * be an output profile (type ‘prtr’).
 */
class OutputIntentPdfx extends AbstractRule
{
    public function doValidate(Document $document, Violations $violations)
    {
        $count = 0;
        $pdfxIntent = null;
        foreach ($document->getDictionary()['Catalog'] as $id) {
            $meta = $document->getObjectById($id);
            $details = $meta->getDetails();

            if (array_key_exists('OutputIntents', $details)) {
                foreach ($details['OutputIntents'] as $intent) {
                    if ($intent['S'] === 'GTS_PDFX') {
                        $count++;
                        $pdfxIntent = $intent;
                    }
                }
            }
        }

        // 1
        if ($count != 1) {
            $violations->add(
                $this->createViolation(
                    sprintf(
                        'The document must contain exactly 1 OutputIntent for PDFX, but it contains %d.',
                        $count
                    )
                )
            );

            return;
        }

        // 2
        if (empty($pdfxIntent['OutputConditionIdentifier'])) {
            $violations->add(
                $this->createViolation('OutputIntent for PDFX OutputConditionIdentifier must not be empty.')
            );
        }

        // 3
        if (!array_key_exists('Info', $pdfxIntent)) {
            $violations->add($this->createViolation('OutputIntent for PDFX must contain an Info key.'));
        }

        // 4
        if ((!array_key_exists('RegistryName', $pdfxIntent) || empty($pdfxIntent['RegistryName']))
            && !array_key_exists('DestOutputProfile', $pdfxIntent)
        ) {
            $violations->add(
                $this->createViolation(
                    'OutputIntent for PDFX must specify a RegistryName, or have a DestOutputProfile key.'
                )
            );
        }

        $profile = null;
        /** @var PdfObject $object */
        foreach ($document->getObjects() as $object) {
            // $pdfxIntent['DestOutputProfile'] contains no identifier, so we must use this crude check...
            if ($object->getDetails() == $pdfxIntent['DestOutputProfile']) {
                $profile = $object;

                break;
            }
        }

        // 5
        if (substr($profile->getContent(), 12, 4) != 'prtr') { // bytes 12-15 contain the type according to the ICC spec
            $violations->add($this->createViolation('DestOutputProfile must be a valid output profile.'));
        }
    }
}
