<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Component\Parser;

/**
 * Represents an e-mail attachment.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Attachment
{
    private $_filename = '';

    private $_contentType = '';

    private $_data = '';

    public function __construct($filename, $contentType, $data)
    {
        $this->_filename = $filename;
        $this->_contentType = $contentType;
        $this->_data = $data;
    }

    public function getFilename()
    {
        return $this->_filename;
    }

    public function getContentType()
    {
        return $this->_contentType;
    }

    public function getData()
    {
        return $this->_data;
    }
}