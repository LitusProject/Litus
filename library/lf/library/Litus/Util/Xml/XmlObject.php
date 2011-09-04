<?php

namespace Litus\Util\Xml;

use \InvalidArgumentException;

use \Litus\Util\UTF8;

class XmlObject {

    /**
     * @var string
     */
    private $_content;

    /**
     * @param string $tag
     * @param array|null $params
     * @param array|string|XmlObject|null $content
     */
    public function __construct($tag, $params = null, $content = null)
    {
        if(($tag === null) || !is_string($tag))
            throw new InvalidArgumentException('Invalid tag');

        $n = "\n";

        if($content === null) {
            if($params === null) {
                $this->_content = '<' . $tag . '/>';
            } else {
                $this->_content .= '<' . $tag;
                foreach($params as $key => $value) {
                    $this->_content .= ' ' . $key . '="' . $this->_escape($value) . '"';
                }
                $this->_content .= '/>';
            }
        } else {
            if($params === null) {
                $this->_content = '<' . $tag . '>';
            } else {
                $this->_content .= '<' . $tag;
                foreach($params as $key => $value) {
                    $this->_content .= ' ' . $key . '="' . $this->_escape($value) . '"';
                }
                $this->_content .= '>';
            }

            if(is_string($content)) {
                $this->_content .= $this->_escape($content);
            } elseif($content instanceof XmlObject) {
                $this->_content .= $n;
                $this->_content .= $content->__toString();
            } elseif(is_array($content)) {
                $this->_content .= $n;
                foreach($content as $part) {
                    if(is_string($part))
                        $this->_content .= $this->_escape($part);
                    elseif($part instanceof XmlObject)
                        $this->_content .= $part->__toString();
                    else
                        throw new InvalidArgumentException('Invalid content.');
                }
            } else throw new InvalidArgumentException('Invalid content.');

            $this->_content .= '</' . $tag . '>' . $n;
        }
    }

    private function _escape($value)
    {
        return UTF8::utf8toHtml($value, false);
    }

    public function __toString()
    {
        return $this->_content;
    }

}
