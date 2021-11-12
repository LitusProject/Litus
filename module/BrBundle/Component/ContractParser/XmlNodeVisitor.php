<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class XmlNodeVisitor implements NodeVisitor
{
    /**
     * @var string
     */
    private $string = '';

    /**
     * @param  Entry $entry
     * @return null
     */
    public function visitEntry(Entry $entry)
    {
        $this->string .= '<entry>';
        foreach ($entry->getNodes() as $node) {
            $node->visitNode($this);
        }

        $this->string .= '</entry>';
    }

    /**
     * @param  Entries $entries
     * @return null
     */
    public function visitEntries(Entries $entries)
    {
        $this->string .= '<entries>';
        foreach ($entries->getEntries() as $entry) {
            $entry->visitNode($this);
        }

        $this->string .= '</entries>';
    }

    /**
     * @param  Text $text
     * @return null
     */
    public function visitText(Text $text)
    {
        $this->string .= $text->getText();
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->string;
    }
}
