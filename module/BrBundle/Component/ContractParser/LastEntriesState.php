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
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class LastEntriesState extends EntryState
{
    /**
     * @var Entries
     */
    private $lastEntries;

    /**
     * @param Entries $entries
     * @param Entry   $entry
     */
    public function __construct(Entries $entries, Entry $entry)
    {
        parent::__construct($entry);
        $this->lastEntries = $entries;
    }

    /**
     * @param  string $text
     * @return self
     */
    public function addEntry($text)
    {
        $entry = new Entry($text);
        $this->lastEntries->addEntry($entry);

        return $this;
    }

    /**
     * @param  string        $text
     * @return LastTextState
     */
    public function addText($text)
    {
        $t = new Text($text);

        $this->getEntry()->addNodeToList($t);

        return new LastTextState($t, $this->getEntry());
    }

    /**
     * @param string $text
     */
    public function passOn($indent, $text)
    {
        $this->lastEntries->passOn($indent, $text);
    }
}
