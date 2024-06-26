<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Entry implements Node
{
    /**
     * @var integer
     */
    private $indent;

    /**
     * @var EntryState
     */
    private $state;

    /**
     * @var array
     */
    private $nodes;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->indent = -1;
        $this->state = new NewState($this);
        $this->parse($text);
    }

    /**
     * @param  integer $indent
     * @return null
     */
    public function setIndent($indent)
    {
        $this->indent = $indent;
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param  string $text
     * @return null
     */
    public function parse($text)
    {
        $indent = $this->nbSpacesLeadingLine($text);
        $rest = substr($text, $indent);

        if (strlen($rest) == 0) {
            return;
        }

        $this->handleLine($indent, $rest);
    }

    /**
     * @param  integer $indent
     * @param  string  $text
     * @return null
     */
    public function handleLine($indent, $text)
    {
        if ($this->indent == -1) {
            $this->indent = $indent;
        } elseif ($indent < $this->indent) {
            throw new IllegalFormatException('Illegal Indent');
        }

        if ($indent == $this->indent) {
            if ($text[0] == '*') {
                $this->parseEntry($text);
            } else {
                $this->parseText($text);
            }
        } else {
            $this->state->passOn($indent - $this->indent, $text);
        }
    }

    /**
     * @param  string $textWithSymbol
     * @return null
     */
    protected function parseEntry($textWithSymbol)
    {
        $textWithSymbol[0] = ' ';
        $textWithoutSymbol = $textWithSymbol;
        $this->state = $this->state->addEntry($textWithoutSymbol);
    }

    /**
     * @param  string $text
     * @return null
     */
    protected function parseText($text)
    {
        $this->state = $this->state->addText($text);
    }

    /**
     * @param  string $line
     * @return integer
     */
    protected function nbSpacesLeadingLine($line)
    {
        $l = strlen($line);
        for ($i = 0; $i < $l; $i++) {
            if ($line[$i] != ' ') {
                break;
            }
        }

        return $i;
    }

    /**
     * @param  NodeVisitor $nodeVisitor
     * @return null
     */
    public function visitNode(NodeVisitor $nodeVisitor)
    {
        $nodeVisitor->visitEntry($this);
    }

    /**
     * @param  Node $node
     * @return null
     */
    public function addNodeToList(Node $node)
    {
        $this->nodes[] = $node;
    }
}
