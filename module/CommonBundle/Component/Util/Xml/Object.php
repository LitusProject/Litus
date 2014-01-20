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

namespace CommonBundle\Component\Util\Xml;

use CommonBundle\Component\Util\Utf8;

/**
 * This class represents an XML object.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Object
{
    /**
     * @var string The object's content
     */
    private $_content;

    /**
     * @param string $tag The object's tag
     * @param array $params The object's paramters
     * @param mixed $content The object's content
     * @throws \CommonBundle\Component\Util\Xml\Exception\InvalidArugmentException The given content was invalid
     */
    public function __construct($tag, array $params = null, $content = null)
    {
        if (($tag === null) || !is_string($tag))
            throw new InvalidArgumentException('Invalid tag');

        if ($content === null) {
            if ($params === null) {
                $this->_content = '<' . $tag . '/>';
            } else {
                $this->_content .= '<' . $tag;
                foreach ($params as $key => $value)
                    $this->_content .= ' ' . $key . '="' . $this->_escape($value) . '"';
                $this->_content .= '/>';
            }
        } else {
            if ($params === null) {
                $this->_content = '<' . $tag . '>';
            } else {
                $this->_content .= '<' . $tag;
                foreach ($params as $key => $value)
                    $this->_content .= ' ' . $key . '="' . $this->_escape($value) . '"';
                $this->_content .= '>';
            }

            if (is_string($content)) {
                $this->_content .= $this->_escape($content);
            } else if ($content instanceof Object) {
                $this->_content .= $content->__toString();
            } else if (is_array($content)) {
                foreach ($content as $part) {
                    if (is_string($part)) {
                        $this->_content .= $this->_escape($part);
                    } else if ($part instanceof Object) {
                        $this->_content .= $part->__toString();
                    } else {
                        throw new Exception\InvalidArgumentException('The given content was invalid');
                    }
                }
            } else {
                throw new Exception\InvalidArgumentException('The given content was invalid');
            }

            $this->_content .= '</' . $tag . '>';
        }
    }

    /**
     * Creates an XML object directly from a given XML string.
     *
     * @param string $xmlString The content of the object
     * @return Object
     */
    public static function fromString($xmlString)
    {
        $result = new Object('tag');
        $result->_content = $xmlString;
        return $result;
    }

    /**
     * Converts an UTF-8 value to HTML.
     *
     * @param string $value The value that should be converted
     * @return string
     */
    private function _escape($value)
    {
        return Utf8::utf8ToHtml($value, true);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->_content;
    }
}
