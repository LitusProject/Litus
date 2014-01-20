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

namespace MailBundle\Component\Parser\Message;

/**
 * Represents an e-mail attachment.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Attachment
{
    /**
     * @var string The filename of the attachment
     */
    private $_filename = '';

    /**
     * @var string The attachment's content-type
     */
    private $_contentType = '';

    /**
     * @var string The binary data of the attachment
     */
    private $_data = '';

    /**
     * @param string $filename The filename of the attachment
     * @param string $contentType The attachment's content-type
     * @param string $data The binary data of the attachment
     */
    public function __construct($filename, $contentType, $data)
    {
        $this->_filename = $filename;
        $this->_contentType = $contentType;
        $this->_data = $data;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }
}