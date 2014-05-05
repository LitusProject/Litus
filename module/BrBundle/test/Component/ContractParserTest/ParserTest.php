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

use StoreBundle\Component\Storage;
use StoreBundle\Component\StorageFactory;
use StoreBundle\Component\Article\ArticleFactory;
use StoreBundle\Component\Unit\UnitFactory;
use StoreBundle\Component\Valuta\ValutaFactory;
use BrBundle\Component\ContractParser\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testOneLiner()
    {
        $parser = new Parser();
        $parser->parse('*  Test');
        var_dump($parser);
    }
    
    public function testTwoLiner()
    {
        $parser = new Parser();
        $parser->parse('*  Two \n* Liner');
        var_dump($parser);
    }
    
    public function testTextNextLine()
    {
        $parser = new Parser();
        $parser->parse('*\n    Next');
        var_dump($parser);        
    }
}