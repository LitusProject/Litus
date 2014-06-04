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
class Entry implements Node
{
    private $indent;
    private $state;

    private $nodes;

    public function __construct($text)
    {
        $this->indent = -1;
        $this->state = new NewState($this);
        $this->parse($text);
    }

    public function setIndent($indent)
    {
        $this->indent = $indent;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function parse($text)
    {
        $indent = $this->nbSpacesLeadingLine($text);
        $rest = substr($text, $indent);

        if(strlen($rest) == 0)

            return;

        $this->handleLine($indent, $rest);
    }

    public function handleLine($indent, $text)
    {
        if($this->indent == -1)
            $this->indent = $indent;
        elseif($indent < $this->indent)
            throw new IllegalFormatException('Illegal Indent');

        if ($indent == $this->indent) {
            if ($text[0] == '*') {
                $this->parseEntry($text);
            } else {
                $this->parseText($text);
            }
        } else {
            $this->state->passOn($indent-$this->indent, $text);
        }
    }

    protected function parseEntry($textWithSymbol)
    {
        $textWithSymbol[0] = ' ';
        $textWithoutSymbol = $textWithSymbol;
        $this->state = $this->state->addEntry($textWithoutSymbol);
    }

    protected function parseText($text)
    {
        $this->state = $this->state->addText($text);
    }

    protected function nbSpacesLeadingLine($line)
    {
        $i = 0;
        $l = strlen($line);
        while ($i < $l) {
            if($line[$i] != ' ')
                break;

            $i++;
        }

        return $i;
    }

    public function visitNode($nodeVisitor)
    {
        $nodeVisitor->visitEntry($this);
    }

    /**
     *
     * @param Node $node
     */
    public function addNodeToList($node)
    {
        $this->nodes[] = $node;
    }
}
