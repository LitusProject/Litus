<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Parser
{
    /**
     * @var EntriesOnlyEntry
     */
    private $rootEntry;

    public function __construct()
    {
        $this->rootEntry = new EntriesOnlyEntry();
    }

    /**
     * @param  string $text
     * @return null
     */
    public function parse($text)
    {
        $lines = explode("\n", $text);

        $lineNb = 1;

        foreach ($lines as $line) {
            $line = rtrim($line);
            try {
                $this->parseLine($line);
            } catch (IllegalFormatException $e) {
                $e->setLineNumber($lineNb);
                throw $e;
            }
            $lineNb++;
        }
    }

    /**
     * @param  string $line
     * @return null
     */
    protected function parseLine($line)
    {
        $this->rootEntry->parse($line);
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $XmlNodeVisitor = new XmlNodeVisitor();
        $this->rootEntry->getEntries()->visitNode($XmlNodeVisitor);

        return $XmlNodeVisitor->getXml();
    }
}
