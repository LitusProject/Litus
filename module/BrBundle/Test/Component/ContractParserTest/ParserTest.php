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

namespace BrBundle\Test\Component\ContractParserTest;

use BrBundle\Component\ContractParser\Parser,
    BrBundle\Component\ContractParser\IllegalFormatException,
    PHPUnit_Framework_TestCase;

class ParserTest extends PHPUnit_Framework_TestCase
{
    protected function parserTester($text, $xml)
    {
        $parser = new Parser();
        $parser->parse($text);
        $this->assertEquals($xml, $parser->getXml());
    }

    public function testOneLiner1()
    {
        $this->parserTester("*  Test",
                "<entries><entry>Test</entry></entries>");
    }

    public function testOneLiner2()
    {
        $this->parserTester(
                "*Test",

                "<entries><entry>Test</entry></entries>");
    }

    public function testTwoLiner()
    {
        $this->parserTester(
                "*  Two\n".
                "*  Liner",

                "<entries>" .
                "<entry>Two</entry>".
                "<entry>Liner</entry>".
                "</entries>");
    }

    public function testClearTrailingSpaces()
    {
        $this->parserTester(
                "*  Two    \n".
                "*  Liner",

                "<entries>" .
                "<entry>Two</entry>".
                "<entry>Liner</entry>".
                "</entries>");
    }

    public function testTextNextLine()
    {
        $this->parserTester("*\n    Next",
                 "<entries><entry>Next</entry></entries>");
    }

    public function testTwoLevels1()
    {
        $t =    "* One\n".
                "  * One Point One";
        $x =    "<entries><entry>One<entries><entry>One Point One".
                "</entry></entries></entry></entries>";

        $this->parserTester($t, $x);
    }

    public function testTwoLevels2()
    {
        $t =   "*  \n" .
               "  One\n" .
               "  * One Point One";
        $x =   "<entries><entry>One<entries><entry>One Point One".
               "</entry></entries></entry></entries>";

        $this->parserTester($t, $x);
    }
    public function testTwoLevels3()
    {
        $parser = new Parser();
        $t =   "*    *  One Point One\n" .
               "        AndSomeMore TExt";

        $x =   "<entries><entry><entries><entry>One Point One\nAndSomeMore TExt" .
               "</entry></entries></entry></entries>";
        $this->parserTester($t, $x);
    }

    public function testIllegalText1()
    {
        $parser = new Parser();
        try {
            $parser->parse("IllegalText");
            $this->fail("No exception thrown");
        } catch (IllegalFormatException $e) {
            $this->assertEquals($e->getLineNumber(), 1);
        }
    }

    public function testIllegalText2()
    {
        $parser = new Parser();
        try {
            $parser->parse("* ValidBullet\nIllegalText");
            $this->fail("No exception thrown");
        } catch (IllegalFormatException $e) {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }

    public function testTextIndentToFar()
    {
        $parser = new Parser();
        try {
            $parser->parse("*  A\n    IllegalText");
            $this->fail('No exception thrown');
        } catch (IllegalFormatException $e) {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }

    public function testTextIndentToShort()
    {
        $parser = new Parser();
        try {
            $parser->parse("*  A\n IllegalText");
            $this->fail('No exception thrown');
        } catch (IllegalFormatException $e) {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }

    public function testEntryIndentToShort()
    {
        $parser = new Parser();
        try {
            $parser->parse("*  A\n *    IllegalText");
            $this->fail('No exception thrown');
        } catch (IllegalFormatException $e) {
            $this->assertEquals($e->getLineNumber(), 2);
        }
    }
}
