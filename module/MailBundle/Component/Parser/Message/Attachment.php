<?php

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
    private $filename = '';

    /**
     * @var string The attachment's content-type
     */
    private $contentType = '';

    /**
     * @var string The binary data of the attachment
     */
    private $data = '';

    /**
     * @param string $filename    The filename of the attachment
     * @param string $contentType The attachment's content-type
     * @param string $data        The binary data of the attachment
     */
    public function __construct($filename, $contentType, $data)
    {
        $this->filename = $filename;
        $this->contentType = $contentType;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
