<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class NewState extends \BrBundle\Component\ContractParser\EntryState
{
    /**
     * @param  string $text
     * @return LastEntriesState
     */
    public function addEntry($text)
    {
        $entries = new Entries($text);

        $this->getEntry()->addNodeToList($entries);

        return new LastEntriesState($entries, $this->getEntry());
    }

    /**
     * @param  string $text
     * @return LastTextState
     */
    public function addText($text)
    {
        $t = new Text($text);

        $this->getEntry()->addNodeToList($t);

        return new LastTextState($t, $this->getEntry());
    }
}
