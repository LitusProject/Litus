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
    private $mailParse;

    /**
     * @var string The message string that should be parsed
     */
    private $message = '';

    /**
     * @var array The different parts of the message
     */
    private $parts = array();

    /**
     * @param string $message The message string that should be parsed
     */
    public function __construct($message)
    {
        $this->message = $message;

        $this->mailParse = mailparse_msg_create();
        mailparse_msg_parse($this->mailParse, $message);

        $this->parse();
    }

    /**
     * Retrieve the message's headers.
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this->getPartHeaders($this->parts[1]);
    }

    /**
     * Retrieve a specific header.
     *
     * @param  string $name The header's name
     * @return string
     */
    public function getHeader($name)
    {
        $headers = $this->getPartHeaders($this->parts[1]);

        $header = '';
        if (isset($headers[$name])) {
            $header = $headers[$name];
        }

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
            'html' => 'text/html',
        );

        $body = array();
        foreach ($this->parts as $part) {
            if (in_array($this->getPartContentType($part), $bodyTypes)) {
                $headers = $this->getPartHeaders($part);
                $content = $this->decode(
                    $this->getPartBody($part),
                    array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : ''
                );

                $body[] = array(
                    'type' => array_search($this->getPartContentType($part), $bodyTypes),
                    'content' => $content,
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
            'inline',
        );

        $headers = array(
            'content-id',
        );

        $attachments = array();
        foreach ($this->parts as $part) {
            $contentDisposition = $this->getPartContentDisposition($part);

            $attachment = null;
            if (in_array($contentDisposition, $contentDispositions) && isset($part['disposition-filename'])) {
                $attachmentData = $this->decode(
                    $this->getPartBody($part),
                    (array_key_exists('content-transfer-encoding', $part['headers']) ? $part['headers']['content-transfer-encoding'] : '')
                );

                $attachment = new Attachment(
                    $part['disposition-filename'],
                    $this->getPartContentType($part),
                    $attachmentData
                );
            } else {
                foreach ($headers as $header) {
                    if (isset($this->getPartHeaders($part)[$header])) {
                        $attachmentData = $this->decode(
                            $this->getPartBody($part),
                            (array_key_exists('content-transfer-encoding', $part['headers']) ? $part['headers']['content-transfer-encoding'] : '')
                        );

                        $filename = $this->getPartHeaders($part)[$header];
                        if (substr($filename, 0, 1) == '<' && substr($filename, -1) == '>') {
                            $filename = substr($filename, 1, (strlen($filename) - 2));
                        }

                        $attachment = new Attachment(
                            $filename,
                            $this->getPartContentType($part),
                            $attachmentData
                        );
                    }
                }
            }

            if (null !== $attachment) {
                $attachments[] = $attachment;
            }
        }

        return $attachments;
    }

    /**
     * Parse the message into its parts.
     *
     * @return void
     */
    private function parse()
    {
        $structure = mailparse_msg_get_structure($this->mailParse);

        $this->parts = array();
        foreach ($structure as $nbPart) {
            $part = mailparse_msg_get_part($this->mailParse, $nbPart);
            $this->parts[$nbPart] = mailparse_msg_get_part_data($part);
        }
    }

    /**
     * @param  array $part
     * @return array
     */
    private function getPartHeaders($part)
    {
        $headers = array();
        if (isset($part['headers'])) {
            $headers = $part['headers'];
        }

        return $headers;
    }

    /**
     * Find the content-type of a part.
     *
     * @param  array  $part The part we want to query
     * @return string
     */
    private function getPartContentType($part)
    {
        $contentType = '';
        if (isset($part['content-type'])) {
            $contentType = $part['content-type'];
            if (false !== strpos($contentType, ';')) {
                $contentType = substr($contentType, 0, strpos($contentType, ';'));
            }
        }

        return $contentType;
    }

    /**
     * Find the content-disposition of a part.
     *
     * @param  array  $part The part we want to query
     * @return string
     */
    private function getPartContentDisposition($part)
    {
        $contentDisposition = '';
        if (isset($part['content-disposition'])) {
            $contentDisposition = $part['content-disposition'];
        }

        return $contentDisposition;
    }

    /**
     * Retrieve a part's body.
     *
     * @param  array  $part The part we want to query
     * @return string
     */
    private function getPartBody($part)
    {
        return substr(
            $this->message,
            $part['starting-pos-body'],
            ($part['ending-pos-body'] - $part['starting-pos-body'])
        );
    }

    /**
     * Decode the given string.
     *
     * @param  string $encodedString The encoded string
     * @param  string $encodingType  The encoding type
     * @return string
     */
    private function decode($encodedString, $encodingType)
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
