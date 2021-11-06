<?php

namespace CommonBundle\Component\Util\Xml;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Node;

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
     * @var boolean Whether or not the file should be destroyed, defaults to true
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
     * Append an XML node to the file.
     *
     * @param  Node $node The node that should be appended
     * @return void
     */
    public function append(Node $node)
    {
        $this->data->appendContent(
            $node->__toString()
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
