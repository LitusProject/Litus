<?php

namespace Litus\Util\Xml;

use \Litus\Util\TmpFile;
use \Litus\Util\Xml\XmlObject;

class XmlGenerator {

    /**
     * @var \Litus\Util\TmpFile
     */
    private $_data;

    /**
     * @var bool
     */
    private $_destroy;

    public function __construct(TmpFile $tmpFile = null)
    {
        if($tmpFile === null) {
            $this->_data = new TmpFile();
            $this->_destroy = true;
        } else {
            $this->_data = $tmpFile;
            $this->_destroy = false;
        }

        $this->_data->appendContent('<?xml version="1.0" encoding="ISO-8859-1"?>');
    }

    public function append(XmlObject $object)
    {
        $this->_data->appendContent($object->__toString());
    }

    public function __destruct()
    {
        if($this->_destroy)
            $this->_data->destroy();
    }

    public function __toString()
    {
        return $this->_data->getContent();
    }

}
