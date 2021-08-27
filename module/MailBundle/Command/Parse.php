<?php

namespace MailBundle\Command;

use MailBundle\Component\Parser\Message as MessageParser;
use MailBundle\Entity\Message;
use MailBundle\Entity\Message\Attachment;

/**
 * Parse mail from input.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Parse extends \CommonBundle\Component\Console\Command
{
    const COMMAND_STORE = '001';

    /**
     * @var array
     */
    public static $commands = array(
        self::COMMAND_STORE => 'Store the incoming mail and its attachments...',
    );

    protected function configure()
    {
        parent::configure();

        $this->setName('mail:parse')
            ->setDescription('Parse mail from input');
    }

    protected function invoke()
    {
        $this->parseMessage(
            $this->readMail()
        );
    }

    /**
     * @param string  $str
     * @param boolean $raw
     */
    public function write($str, $raw = false)
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->logMessage($str);
        }

        parent::write($str, $raw);
    }

    /**
     * @param string  $str
     * @param boolean $raw
     */
    public function writeln($str, $raw = false)
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->logMessage($str);
        }

        parent::writeln($str, $raw);
    }

    /**
     * @param string $str
     */
    private function logMessage($str)
    {
        $this->getSentryClient()->logMessage($str);
    }

    /**
     * @return string
     */
    private function readMail()
    {
        $stdinStream = fopen('php://stdin', 'r');
        $message = '';
        while (!feof($stdinStream)) {
            $line = fread($stdinStream, 1024);
            $message .= $line;
        }
        fclose($stdinStream);

        return $message;
    }

    /**
     * @param string $message
     */
    private function parseMessage($message)
    {
        $parser = new MessageParser($message);
        $command = substr($parser->getSubject(), 2, 3);

        switch ($command) {
            case self::COMMAND_STORE:
                $this->storeMessage($parser);
                break;
            default:
                $this->writeln('Invalid command specified in the subject line (' . $command . ')');
                break;
        }
    }

    /**
     * @param MessageParser $parser
     */
    private function storeMessage(MessageParser $parser)
    {
        $body = null;
        if (count($parser->getBody()) > 1) {
            foreach ($parser->getBody() as $itBody) {
                if ($itBody['type'] == 'html') {
                    $body = $itBody;
                    break;
                }
            }
        } else {
            $body = $parser->getBody()[0];
        }

        if ($body !== null) {
            $newMessage = new Message(
                $body['type'],
                substr($parser->getSubject(), 7),
                $body['content']
            );

            $this->getEntityManager()->persist($newMessage);

            foreach ($parser->getAttachments() as $attachment) {
                $newAttachment = new Attachment(
                    $newMessage,
                    $attachment->getFilename(),
                    $attachment->getContentType(),
                    $attachment->getData()
                );

                $this->getEntityManager()->persist($newAttachment);
            }

            $this->getEntityManager()->flush();

            $this->writeln('Storing incoming message with subject "' . substr($parser->getSubject(), 7) . '"');
        }
    }
}
