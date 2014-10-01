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
abstract class EntryState
{
    private $entry;

    public function __construct($entry)
    {
        $this->entry = $entry;
    }

    protected function getEntry()
    {
        return $this->entry;
    }

    public function passOn($indent, $text)
    {
        throw new IllegalFormatException("Check your indents.");
    }

    abstract public function addEntry($text);

    abstract public function addText($text);
}
