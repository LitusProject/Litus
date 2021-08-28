<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
abstract class EntryState
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return Entry
     */
    protected function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param integer $indent
     * @param string  $text
     */
    public function passOn($indent, $text)
    {
        throw new IllegalFormatException('Check your indents.');
    }

    /**
     * @param  string $text
     * @return self
     */
    abstract public function addEntry($text);

    /**
     * @param  string $text
     * @return self
     */
    abstract public function addText($text);
}
