<?php

namespace BrBundle\Component\ContractParser;

use Exception;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class EntriesOnlyEntry extends \BrBundle\Component\ContractParser\Entry
{
    public function __construct()
    {
        parent::__construct('');
    }

    /**
     * @param  Node $node
     * @return null
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
            throw new Exception('There should be only one or zero entries.');
        }
    }
}
