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
     * @var TmpFile The file where the data will be saved to
     */
    private $data = null;

    /**
     * @var bool Whether or not the file should be destroyed, defaults to true
     */
    private $destroy = true;

    /**
     * @param TmpFile|null $tmpFile The file where the data will be saved to
     */
    public function __construct(TmpFile $tmpFile = null, $headerKeys = null)
    {
        if ($tmpFile === null) {
            $this->data = new TmpFile();
            $this->destroy = true;
        } else {
            $this->data = $tmpFile;
            $this->destroy = false;
        }

        if ($headerKeys === null) {
            $this->data->appendContent('<?xml version="1.0" encoding="ISO-8859-1"?>');
        } else {
            $this->data->appendContent('<?xml ' . $headerKeys . '?>');
        }
    }

    /**
     * Append an XML object to the file.
     *
     * @param  Object $object The object that should be appended
     * @return void
     */
    public function append(Object $object)
    {
        $this->data->appendContent(
            $object->__toString()
        );
    }

    public function __toString()
    {
        return $this->data->getContent();
    }

    public function __destruct()
    {
        if ($this->destroy) {
            $this->data->destroy();
        }
    }
}
