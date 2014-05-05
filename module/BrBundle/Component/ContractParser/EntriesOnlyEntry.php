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

use CommonBundle\Component\Util\Xml\Object as XmlObject;

/**
 * 
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class EntriesOnlyEntry extends Entry
{
    protected function parseText($text)
    {
        throw new IllegalFormatException();
    }
    
    public function getEntries()
    {
        if(count($this->getNodes()) == 0)
            return null;
        
        if(count($this->getNodes()) == 1)
            return $this->getNodes()[0];
        else
            throw new \Exception("There should be only one or zero entries.");
    }
}