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
class XmlNodeVisitor
{
    private $string = '';

    public function visitEntry($entry)
    {
        $this->string .= '<entry>';
        foreach ($entry->getNodes() as $node) {
            $node->visitNode($this);
        }
        $this->string .= '</entry>';
    }
    public function visitEntries($entries)
    {
        $this->string .= '<entries>';
        foreach ($entries->getEntries() as $entry) {
            $entry->visitNode($this);
        }
        $this->string .= '</entries>';
    }
    public function visitText($text)
    {
        $this->string .= $text->getText();
    }

    public function getXml()
    {
        return $this->string;
    }
}
