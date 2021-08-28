<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Entries extends \BrBundle\Component\ContractParser\EntryItem
{
    /**
     * @var Entry
     */
    private $lastEntry;

    /**
     * @var array
     */
    private $entries;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->entries = array();
        $this->lastEntry = new Entry($text);
        $this->entries[] = $this->lastEntry;
    }

    /**
     * @param  string $text
     * @return null
     */
    public function parse($text)
    {
        $this->lastEntry->parse($text);
    }

    /**
     * @param  integer $indent
     * @param  string  $text
     * @return null
     */
    public function passOn($indent, $text)
    {
        $this->lastEntry->handleLine($indent, $text);
    }

    /**
     * @param  Entry $entry
     * @return null
     */
    public function addEntry(Entry $entry)
    {
        $this->entries[] = $entry;
        $this->lastEntry = $entry;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param  NodeVisitor $nodeVisitor
     * @return null
     */
    public function visitNode(NodeVisitor $nodeVisitor)
    {
        $nodeVisitor->visitEntries($this);
    }
}
