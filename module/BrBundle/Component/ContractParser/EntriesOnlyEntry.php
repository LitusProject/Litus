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

use Exception;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class EntriesOnlyEntry extends Entry
{
    public function __construct()
    {
        parent::__construct('');
    }

    /**
     * @param Node $node
     */
    public function addNodeToList(Node $node)
    {
        if ($node instanceof Text) {
            throw new IllegalFormatException('There is no text allowed without a parent-entry at the place');
        }

        parent::addNodeToList($node);
    }

    /**
     * @return Node|null
     */
    public function getEntries()
    {
        if (count($this->getNodes()) == 0) {
            return null;
        }

        if (count($this->getNodes()) == 1) {
            return $this->getNodes()[0];
        } else {
            throw new Exception("There should be only one or zero entries.");
        }
    }
}
