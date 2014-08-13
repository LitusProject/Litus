<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Component\ContractParser;

/**
 *
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Parser
{
    private $rootEntry;

    public function __construct()
    {
        $this->rootEntry = new EntriesOnlyEntry();
    }

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

    protected function parseLine($line)
    {
        $this->rootEntry->parse($line);
    }

    public function getXml()
    {
        $XmlNodeVisitor = new XmlNodeVisitor();
        $this->rootEntry->getEntries()->visitNode($XmlNodeVisitor);

        return $XmlNodeVisitor->getXml();
    }
}
