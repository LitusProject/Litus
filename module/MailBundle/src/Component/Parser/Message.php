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

use MailBundle\Component\Parser\Attachment;

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

    private $_message = '';

    private $_parts = array();

    private $_body = array();

    private $_attachments = array();

    public function  __construct($message)
    {
        $this->_message = $message;

        $this->_mailParse = mailparse_msg_create();
        mailparse_msg_parse($this->_mailParse, $message);

        $this->_parse();
    }

    public function getHeaders()
    {
        return $this->_getPartHeaders($this->_parts[1]);
    }

    public function getHeader($name) {
        $headers = $this->_getPartHeaders($this->_parts[1]);
        
        $header = '';
        if (isset($headers[$name]))
            $header = $headers[$name];

        return $header;
    }

    public function getSubject()
    {
        return $this->getHeader('subject');
    }

    public function getBody($type = 'html')
    {
        $bodyTypes = array(
            'text' => 'text/plain',
            'html' => 'text/html'
        );

        $body = '';
        if (in_array($type, array_keys($bodyTypes))) {
            foreach($this->_parts as $part) {
                if ($this->_getPartContentType($part) == $bodyTypes[$type]) {
                    $headers = $this->_getPartHeaders($part);

                    $body = $this->_decode(
                        $this->_getPartBody($part),
                        array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : ''
                    );

                    break;
                }
            }
        } else {
            throw new Exception\InvalidArgumentException('Type can either be text or html');
        }

        return $body;
    }

    public function getAttachments()
    {
        $contentDispositions = array(
            'attachment',
            'inline'
        );

        $attachments = array();
        foreach($this->_parts as $part) {
            $contentDisposition = $this->_getPartContentDisposition($part);

            if (in_array($contentDisposition, $contentDispositions) && isset($part['disposition-filename'])) {
                $attachmentData = $this->_decode(
                    $this->_getPartBody($part),
                    (array_key_exists('content-transfer-encoding', $part['headers']) ? $part['headers']['content-transfer-encoding'] : '')
                );

                $attachments[] = new Attachment(
                    $part['disposition-filename'],
                    $this->_getPartContentType($part),
                    $attachmentData
                );
            }
        }

        return $attachments;
    }

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

    private function _getPartContentDisposition($part)
    {
        $contentDisposition = '';
        if (isset($part['content-disposition']))
            $contentDisposition = $part['content-disposition'];

        return $contentDisposition;
    }

    private function _getPartBody($part)
    {
        return substr(
            $this->_message,
            $part['starting-pos-body'],
            ($part['ending-pos-body'] - $part['starting-pos-body'])
        );
    }

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
