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
class Entries extends EntryItem
{
    private $lastEntry;
    private $entries;

    public function __construct($text)
    {
        $this->entries = [];
        $this->lastEntry = new Entry($text);
        $this->entries[] = $this->lastEntry;
    }

    public function parse($text)
    {
        $this->lastEntry->parse($text);
    }

    public function passOn($indent, $text)
    {
        $this->lastEntry->handleLine($indent, $text);
    }

    public function addEntry($entry)
    {
        $this->entries[] = $entry;
        $this->lastEntry = $entry;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function visitNode($nodeVisitor)
    {
        $nodeVisitor->visitEntries($this);
    }
}
