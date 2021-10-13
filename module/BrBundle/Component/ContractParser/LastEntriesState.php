<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class LastEntriesState extends \BrBundle\Component\ContractParser\EntryState
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
     * @param  string $text
     * @return LastTextState
     */
    public function addText($text)
    {
        $t = new Text($text);

        $this->getEntry()->addNodeToList($t);

        return new LastTextState($t, $this->getEntry());
    }

    /**
     * @param  string $text
     * @return null
     */
    public function passOn($indent, $text)
    {
        $this->lastEntries->passOn($indent, $text);
    }
}
