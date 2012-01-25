<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Util\Xml;

use CommonBundle\Component\Util\File\TmpFile,
	CommonBundle\Component\Util\Xml\Object;

/**
 * This generator creates an XML file from the supplied XML objects
 *
 * @autor Bram Gotink <bram.gotink@litus.cc>
 */
class Generator
{
    /**
     * @var \CommonBundle\Component\Util\TmpFile The file where the data will be saved to
     */
    private $_data = null;

    /**
     * @var bool Whether or not the file should be destroyed, defaults to true
     */
    private $_destroy = true;
	
	/**
	 * @param \CommonBundle\Component\Util\File\TmpFile $tmpFile The file where the data will be saved to
	 */
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
	
	/**
	 * Append an XML object to the file.
	 *
	 * @param \CommonBunle\Component\Util\Xml\Object $object The object that should be appended
	 * @return void
	 */
    public function append(XmlObject $object)
    {
        $this->_data->appendContent(
        	$object->__toString()
        );
    }

    public function __toString()
    {
        return $this->_data->getContent();
    }
    
    public function __destruct()
    {
        if($this->_destroy)
            $this->_data->destroy();
    }
}
