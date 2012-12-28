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
 * Checks whether a mailing list name is unique or not. This class is originally from:
 * {@link https://github.com/plancake/official-library-php-email-parser}.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Message
{
    const PLAINTEXT = 1;
    const HTML = 2;

    /**
     * @var boolean
     */
    private $_isImapExtensionAvailable = false;

    /**
     *
     * @var string
     */
    private $_emailRawContent = '';

    /**
     *
     * @var array
     */
    protected $_rawFields = array();

    /**
     *
     * @var array
     */
    protected $_rawBodyLines = array();

    /**
     *
     * @param string $_emailRawContent
     */
    public function  __construct($_emailRawContent) {
        $this->_emailRawContent = $_emailRawContent;

        $this->_extractHeadersAndRawBody();

        if (function_exists('imap_open'))
            $this->_isImapExtensionAvailable = true;
    }

    /**
     *
     * @return string (in UTF-8 format)
     * @throws Exception if a subject header is not found
     */
    public function getSubject()
    {
        if (!isset($this->_rawFields['subject']))
            throw new Exception\RuntimeException('Couldn\'t find the subject of the email');

        $subject = '';
        if ($this->_isImapExtensionAvailable) {
            foreach (imap_mime_header_decode($this->_rawFields['subject']) as $h) {
                $charset = ($h->charset == 'default') ? 'US-ASCII' : $h->charset;
                $subject .=  iconv($charset, "UTF-8//TRANSLIT", $h->text);
            }
        } else {
            $subject = utf8_encode(iconv_mime_decode($this->_rawFields['subject']));
        }

        return $subject;
    }

    /**
     *
     * @return array
     */
    public function getCc()
    {
        if (!isset($this->_rawFields['cc']))
            return array();

        return explode(',', $this->_rawFields['cc']);
    }

    /**
     *
     * @return array
     * @throws Exception if a to header is not found or if there are no recipient
     */
    public function getTo()
    {
        if ( (!isset($this->_rawFields['to'])) || (!count($this->_rawFields['to'])))
            throw new Exception\RuntimeException('Couldn\'t find the recipients of the email');

        return explode(',', $this->_rawFields['to']);
    }

    /**
     * Return a string with the message body, UTF-8 encoded.
     *
     * Example of an e-mail body:
     *
     *   --0016e65b5ec22721580487cb20fd
     *   Content-Type: text/plain; charset=ISO-8859-1
     *
     *   Hi all. I am new to Android development.
     *   Please help me.
     *
     *   --
     *   My signature
     *
     *   email: myemail@gmail.com
     *   web: http://www.example.com
     *
     *   --0016e65b5ec22721580487cb20fd
     *   Content-Type: text/html; charset=ISO-8859-1
     *
     * @param integer $returnType The MIME type used to return the body
     * @return string
     */
    public function getBody($returnType = self::PLAINTEXT)
    {
        $body = '';
        $detectedContentType = false;
        $contentTransferEncoding = null;
        $charset = 'ASCII';
        $waitingForContentStart = true;

        $contentTypeRegex = $returnType == self::HTML ? '/^Content-Type: ?text\/html/i' : '/^Content-Type: ?text\/plain/i';

        preg_match_all('!boundary=(.*)$!mi', $this->_emailRawContent, $matches);
        $boundaries = $matches[1];
        foreach($boundaries as $i => $v)
            $boundaries[$i] = str_replace(array("'", '"'), '', $v);

        foreach ($this->_rawBodyLines as $line) {
            if (!$detectedContentType) {
                if (preg_match($contentTypeRegex, $line, $matches))
                    $detectedContentType = true;

                if (preg_match('/charset=(.*)/i', $line, $matches))
                    $charset = strtoupper(trim($matches[1], '"'));
            } elseif ($detectedContentType && $waitingForContentStart) {
                if (preg_match('/charset=(.*)/i', $line, $matches))
                    $charset = strtoupper(trim($matches[1], '"'));

                if ($contentTransferEncoding == null && preg_match('/^Content-Transfer-Encoding: ?(.*)/i', $line, $matches))
                    $contentTransferEncoding = $matches[1];

                if ($this->_isNewLine($line))
                    $waitingForContentStart = false;
            } else {
                if (is_array($boundaries)) {
                    if (in_array(substr($line, 2), $boundaries))
                        break;
                }

                $body .= $line . "\n";
            }
        }

        if (!$detectedContentType)
            $body = implode("\n", $this->_rawBodyLines);

        $body = preg_replace('/((\r?\n)*)$/', '', $body);

        if ($contentTransferEncoding == 'base64')
            $body = base64_decode($body);
        elseif ($contentTransferEncoding == 'quoted-printable')
            $body = quoted_printable_decode($body);

        if ('UTF-8' != $charset) {
            $charset = str_replace("FORMAT=FLOWED", "", $charset);

            $body = iconv($charset, 'UTF-8//TRANSLIT', $body);

            if (false === $body)
                $body = utf8_encode($body);
        }

        return $body;
    }

    /**
     * Return the body in plaintext.
     *
     * @return string
     */
    public function getPlainBody()
    {
        return $this->getBody(self::PLAINTEXT);
    }

    /**
     * Return the body in HTML.
     *
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->getBody(self::HTML);
    }

    /**
     * Return the header with the given name.
     *
     * @param string $headerName The header we want to retrieve
     * @return string
     */
    public function getHeader($headerName)
    {
        $headerName = strtolower($headerName);

        if (isset($this->_rawFields[$headerName]))
            return $this->_rawFields[$headerName];

        return '';
    }

    /**
     * Extract the headers and body from the message string.
     *
     * @return void
     */
    private function _extractHeadersAndRawBody()
    {
        $lines = preg_split("/(\r?\n|\r)/", $this->_emailRawContent);

        $currentHeader = '';

        $i = 0;
        foreach ($lines as $line) {
            if ($this->_isNewLine($line)) {
                $this->_rawBodyLines = array_slice($lines, $i);
                break;
            }

            if ($this->_isLineStartingWithPrintableChar($line)) {
                preg_match('/([^:]+): ?(.*)$/', $line, $matches);
                $newHeader = strtolower($matches[1]);
                $value = $matches[2];
                $this->_rawFields[$newHeader] = $value;
                $currentHeader = $newHeader;
            } else {
                if ($currentHeader)
                    $this->_rawFields[$currentHeader] .= substr($line, 1);
            }

            $i++;
        }
    }

    /**
     * Check whether or not the given line is a newline.
     *
     * @param string $line The line we want to check
     * @return boolean
     */
    private function _isNewLine($line)
    {
        $line = str_replace("\r", '', $line);
        $line = str_replace("\n", '', $line);

        return (strlen($line) == 0);
    }

    /**
     * Check whether or not the given line starts with a character that can be printed.
     *
     * @param string $line The line we want to check
     * @return boolean
     */
    private function _isLineStartingWithPrintableChar($line)
    {
        return preg_match('/^[A-Za-z]/', $line);
    }
}
