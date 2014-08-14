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
class LastTextState extends EntryState
{
    /**
     * @var Text
     */
    private $lastText;

    /**
     *
     * @param Text  $lastText
     * @param Entry $entry
     */
    public function __construct($lastText, $entry)
    {
        parent::__construct($entry);
        $this->lastText = $lastText;
    }

    public function addEntry($text)
    {
        $entries = new Entries($text);

        $this->getEntry()->addNodeToList($entries);

        return new LastEntriesState($entries, $this->getEntry());
    }

    public function addText($text)
    {
        $this->lastText->append("\n" . $text);

        return $this;
    }
}
