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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Util\Xml;

use CommonBundle\Component\Util\Utf8;

/**
 * This class represents an XML node.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Node
{
    /**
     * @var string The node's content
     */
    private $content;

    /**
     * @param  string     $tag     The node's tag
     * @param  array|null $params  The node's paramters
     * @param  mixed|null $content The node's content
     * @throws \CommonBundle\Component\Util\Xml\Exception\InvalidArugmentException The given content was invalid
     */
    public function __construct($tag, array $params = null, $content = null)
    {
        if (($tag === null) || !is_string($tag)) {
            throw new InvalidArgumentException('Invalid tag');
        }

        if ($content === null) {
            if ($params === null) {
                $this->content = '<' . $tag . '/>';
            } else {
                $this->content .= '<' . $tag;
                foreach ($params as $key => $value) {
                    $this->content .= ' ' . $key . '="' . $this->escape($value) . '"';
                }
                $this->content .= '/>';
            }
        } else {
            if ($params === null) {
                $this->content = '<' . $tag . '>';
            } else {
                $this->content .= '<' . $tag;
                foreach ($params as $key => $value) {
                    $this->content .= ' ' . $key . '="' . $this->escape($value) . '"';
                }
                $this->content .= '>';
            }

            if (is_string($content)) {
                $this->content .= $this->escape($content);
            } elseif ($content instanceof Node) {
                $this->content .= $content->__toString();
            } elseif (is_array($content)) {
                foreach ($content as $part) {
                    if (is_string($part)) {
                        $this->content .= $this - _escape($part);
                    } elseif ($part instanceof Node) {
                        $this->content .= $part->__toString();
                    } else {
                        throw new Exception\InvalidArgumentException('The given content was invalid');
                    }
                }
            } else {
                throw new Exception\InvalidArgumentException('The given content was invalid');
            }

            $this->content .= '</' . $tag . '>';
        }
    }

    /**
     * Creates an XML node directly from a given XML string.
     *
     * @param  string $xmlString The content of the node
     * @return Node
     */
    public static function fromString($xmlString)
    {
        $result = new Node('tag');
        $result->content = $xmlString;

        return $result;
    }

    /**
     * Converts an UTF-8 value to HTML.
     *
     * @param  string $value The value that should be converted
     * @return string|null
     */
    private function escape($value)
    {
        return Utf8::utf8ToHtml($value, true);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}
