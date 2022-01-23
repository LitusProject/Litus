<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Text extends \BrBundle\Component\ContractParser\EntryItem
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @param  string $text
     * @return null
     */
    public function append($text)
    {
        $this->text .= $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param  NodeVisitor $nodeVisitor
     * @return null
     */
    public function visitNode(NodeVisitor $nodeVisitor)
    {
        $nodeVisitor->visitText($this);
    }
}
