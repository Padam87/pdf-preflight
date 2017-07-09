<?php

namespace Padam87\PdfPreflight\Parser\Smalot;

use Smalot\PdfParser\Document;

class Parser extends \Smalot\PdfParser\Parser
{
    /**
     * {@inheritdoc}
     */
    public function parseFile($filename, $decode = true)
    {
        return @$this->parseContent(file_get_contents($filename), $decode);
    }

    /**
     * {@inheritdoc}
     */
    public function parseContent($content, $decode = true)
    {
        // Create structure using TCPDF Parser.
        ob_start();

        @$parser = new \Padam87\PdfPreflight\Parser\Parser();
        list($xref, $data) = $parser->parse($content, $decode);
        unset($parser);

        ob_end_clean();

        if (isset($xref['trailer']['encrypt'])) {
            throw new \Exception('Secured pdf file are currently not supported.');
        }

        if (empty($data)) {
            throw new \Exception('Object list not found. Possible secured file.');
        }

        // Create destination object.
        $document = new Document();
        $this->objects = [];

        foreach ($data as $id => $structure) {
            $this->parseObject($id, $structure, $document);
            unset($data[$id]);
        }

        $document->setTrailer($this->parseTrailer($xref['trailer'], $document));
        $document->setObjects($this->objects);

        return $document;
    }
}
