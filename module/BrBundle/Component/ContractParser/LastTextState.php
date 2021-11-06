<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class LastTextState extends \BrBundle\Component\ContractParser\EntryState
{
    /**
     * @var Text
     */
    private $lastText;

    /**
     * @param Text  $lastText
     * @param Entry $entry
     */
    public function __construct(Text $lastText, Entry $entry)
    {
        parent::__construct($entry);
        $this->lastText = $lastText;
    }

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
     * @return self
     */
    public function addText($text)
    {
        $this->lastText->append("\n" . $text);

        return $this;
    }
}
