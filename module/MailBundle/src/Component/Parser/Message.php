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

use MailBundle\Component\Parser\Message\Attachment;

/**
 * Parse a raw e-mail and create a useful object. Partially adapted from
 * {@link http://php-mime-mail-parser.googlecode.com}.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Message
{
    /**
     * @var resource The MailParse resource
     */
    private $_mailParse;

    /**
     * @var string The message string that should be parsed
     */
    private $_message = '';

    /**
     * @var array The different parts of the message
     */
    private $_parts = array();

    /**
     * @param string $message The message string that should be parsed
     */
    public function  __construct($message)
    {
        $this->_message = $message;

        $this->_mailParse = mailparse_msg_create();
        mailparse_msg_parse($this->_mailParse, $message);

        $this->_parse();
    }

    /**
     * Retrieve the message's headers.
     * 
     * @return string
     */
    public function getHeaders()
    {
        return $this->_getPartHeaders($this->_parts[1]);
    }

    /**
     * Retrieve a specific header.
     * 
     * @param string $name The header's name
     * @return string
     */
    public function getHeader($name) {
        $headers = $this->_getPartHeaders($this->_parts[1]);
        
        $header = '';
        if (isset($headers[$name]))
            $header = $headers[$name];

        return $header;
    }

    /**
     * Returns the message's subject.
     * 
     * @return string
     */
    public function getSubject()
    {
        return $this->getHeader('subject');
    }

    /**
     * Retrieves all text/plain and text/html parts from the message.
     * 
     * @return array
     */
    public function getBody()
    {
        $bodyTypes = array(
            'text' => 'text/plain',
            'html' => 'text/html'
        );

        $body = array();
        foreach($this->_parts as $part) {
            if (in_array($this->_getPartContentType($part), $bodyTypes)) {
                $headers = $this->_getPartHeaders($part);
                $content = $this->_decode(
                    $this->_getPartBody($part),
                    array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : ''
                );

                $body[] = array(
                    'type' => array_search($this->_getPartContentType($part), $bodyTypes),
                    'content' => $content
                );
            }
        }

        return $body;
    }

    /**
     * Returns an array with the message's attachments.
     * 
     * @return array
     */
    public function getAttachments()
    {
        $contentDispositions = array(
            'attachment',
            'inline'
        );

        $headers = array(
            'content-id'
        );

        $attachments = array();
        foreach($this->_parts as $part) {
            $contentDisposition = $this->_getPartContentDisposition($part);

            $attachment = null;
            if (in_array($contentDisposition, $contentDispositions) && isset($part['disposition-filename'])) {
                $attachmentData = $this->_decode(
                    $this->_getPartBody($part),
                    (array_key_exists('content-transfer-encoding', $part['headers']) ? $part['headers']['content-transfer-encoding'] : '')
                );

                $attachment = new Attachment(
                    $part['disposition-filename'],
                    $this->_getPartContentType($part),
                    $attachmentData
                );
            } else {
                foreach ($headers as $header) {
                    if (isset($this->_getPartHeaders($part)[$header])) {
                        $attachmentData = $this->_decode(
                            $this->_getPartBody($part),
                            (array_key_exists('content-transfer-encoding', $part['headers']) ? $part['headers']['content-transfer-encoding'] : '')
                        );
                        
                        $filename = $this->_getPartHeaders($part)[$header];
                        if (substr($filename), 1, 1) == '<' && substr($filename), -1) == '>')
                            $filename = substr($filename, 2, (strlen($filename) - 1));
                        
                        $attachment = new Attachment(
                            $filename,
                            $this->_getPartContentType($part),
                            $attachmentData
                        );
                    }
                }
            }

            if (null !== $attachment)
                $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * Parse the message into its parts.
     * 
     * @return void
     */
    private function _parse()
    {
        $structure = mailparse_msg_get_structure($this->_mailParse);

        $this->_parts = array();
        foreach($structure as $nbPart) {
            $part = mailparse_msg_get_part($this->_mailParse, $nbPart);
            $this->_parts[$nbPart] = mailparse_msg_get_part_data($part);
        }
    }

    private function _getPartHeaders($part)
    {
        $headers = array();
        if (isset($part['headers']))
            $headers = $part['headers'];

        return $headers;
    }

    /**
     * Find the content-type of a part.
     * 
     * @param array $part The part we want to query
     * @return string
     */
    private function _getPartContentType($part)
    {
        $contentType = '';
        if (isset($part['content-type'])) {
            $contentType = $part['content-type'];
            if (false !== strpos($contentType, ';'))
                $contentType = substr($contentType, 0, strpos($contentType, ';'));
        }

        return $contentType;
    }

    /**
     * Find the content-disposition of a part.
     * 
     * @param array $part The part we want to query
     * @return string
     */
    private function _getPartContentDisposition($part)
    {
        $contentDisposition = '';
        if (isset($part['content-disposition']))
            $contentDisposition = $part['content-disposition'];

        return $contentDisposition;
    }

    /**
     * Retrieve a part's body.
     * 
     * @param array $part The part we want to query
     * @return string
     */
    private function _getPartBody($part)
    {
        return substr(
            $this->_message,
            $part['starting-pos-body'],
            ($part['ending-pos-body'] - $part['starting-pos-body'])
        );
    }

    /**
     * Decode the given string.
     * 
     * @param string $encodedString The encoded string
     * @param string $encodingType  The encoding type
     * @return string
     */
    private function _decode($encodedString, $encodingType)
    {
        if (strtolower($encodingType) == 'base64') {
            return base64_decode($encodedString);
        } elseif (strtolower($encodingType) == 'quoted-printable') {
             return quoted_printable_decode($encodedString);
        } else {
            return $encodedString;
        }
    }
}
