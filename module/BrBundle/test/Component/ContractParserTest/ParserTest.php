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
use BrBundle\Component\ContractParser\IllegalFormatException;

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testOneLiner1()
    {
        $parser = new Parser();
        $parser->parse('*  Test');
        $xml = '<entries><entry>Test</entry></entries>';
        $this->assertEquals($xml, $parser->getXml());
    }
    
    public function testOneLiner2()
    {
        $parser = new Parser();
        $parser->parse('*Test');
    }
    
    public function testTwoLiner()
    {
        $parser = new Parser();
        $parser->parse('*  Two \n* Liner');
    }
    
    public function testTextNextLine()
    {
        $parser = new Parser();
        $parser->parse('*\n    Next');
    }
    
    public function testTwoLevels1()
    {
        $parser = new Parser();
        $parser->parse('* One\n'+
                       '  * One Point One');
    }
    
    public function testTwoLevels2()
    {
        $parser = new Parser();
        $parser->parse('*  \n' + 
                       '  One\n' + 
                       '  * One Point One');
    }
    public function testTwoLevels3()
    {
        $parser = new Parser();
        $parser->parse('*    *  One Point One\n' + 
                       '        AndSomeMore TExt');
    }
    
    public function testIllegalText1()
    {
        $parser = new Parser();
        try{
            $parser->parse('IllegalText');
            $this->fail('No exception thrown');
        }catch (IllegalFormatException $e)
        {
            $this->assertEquals($e->getLineNumber(), 1);
        }
    }
    
    public function testIllegalText2()
    {
        $parser = new Parser();
        try{
            $parser->parse('* ValidBullet\nIllegalText');
            $this->fail('No exception thrown');
        }catch (IllegalFormatException $e)
        {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }
    
    public function testTextIndentToFar()
    {
        $parser = new Parser();
        try{
            $parser->parse('*  A\n    IllegalText');
            $this->fail('No exception thrown');
        }catch (IllegalFormatException $e)
        {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }
    
    public function testTextIndentToShort()
    {
        $parser = new Parser();
        try{
            $parser->parse('*  A\n IllegalText');
            $this->fail('No exception thrown');
        }catch (IllegalFormatException $e)
        {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }
    
    public function testEntryIndentToShort()
    {
        $parser = new Parser();
        try{
            $parser->parse('*  A\n *    IllegalText');
            $this->fail('No exception thrown');
        }catch (IllegalFormatException $e)
        {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }
}