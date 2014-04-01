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

namespace MailBundle\Command;

use MailBundle\Document\Message,
    MailBundle\Document\Message\Attachment,
    MailBundle\Component\Parser\Message as MessageParser;

/**
 * Parser
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Parser extends \CommonBundle\Component\Console\Command
{
    const COMMAND_STORE = '001';

    /**
     * The available commands
     *
     * @var array
     */
    public static $commands = array(
        self::COMMAND_STORE => 'Store the incoming mail and its attachments...',
    );

    /**
     * @var \CommonBundle\Component\Lilo\Client|null
     */
    private $_lilo;

    protected function configure()
    {
        $this
            ->setName('mail:parse')
            ->setDescription('parse a mail from input')
            ->setHelp(<<<EOT
The %command.name% command reads <comment>php://stdin</comment>, parses
incoming mail and stores it in the document storage.
EOT
        );
    }

    protected function executeCommand()
    {
        if ($this->getServiceLocator()->has('lilo'))
            $this->_lilo = $this->getServiceLocator()->get('lilo');
        else
            $this->_lilo = null;

        $mail = $this->_readMail();
        $this->_parseMessage($mail);
    }

    protected function getLogName()
    {
        return 'MailParser';
    }

    protected function write($str, $raw = false)
    {
        if (null !== $this->_lilo) {
            $this->_sendToLilo($str);
        } else {
            return parent::write($str, $raw);
        }
    }

    protected function writeln($str, $raw = false)
    {
        if (null !== $this->_lilo) {
            $this->_sendToLilo($str);
        } else {
            return parent::writeln($str, $raw);
        }
    }

    private function _sendToLilo($str)
    {
        $this->_lilo->sendLog(
            $str,
            array(
                'MailBundle',
                'parser.php'
            )
        );
    }

    private function _readMail()
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

    private function _parseMessage($message)
    {
        $parser = new MessageParser($message);
        $command = substr($parser->getSubject(), 2, 3);

        switch ($command) {
            case self::COMMAND_STORE:
                $this->_storeMessage($parser);
                break;
            default:
                $this->writeln('Invalid command specified in the subject line (' . $command . ')');
                break;
        }
    }

    private function _storeMessage(MessageParser $parser)
    {
        $attachments = array();
        foreach ($parser->getAttachments() as $attachment) {
            $attachments[] = new Attachment(
                $attachment->getFilename(),
                $attachment->getContentType(),
                $attachment->getData()
            );
        }

        $body = null;
        if (count($parser->getBody()) > 1) {
            foreach ($parser->getBody() as $itBody) {
                if ('html' == $itBody['type']) {
                    $body = $itBody;
                    break;
                }
            }
        } else {
            $body = $parser->getBody()[0];
        }

        if (null !== $body) {
            $newMessage = new Message(
                $body['type'],
                substr($parser->getSubject(), 7),
                $body['content'],
                $attachments
            );

            $this->getDocumentManager()->persist($newMessage);
            $this->getDocumentManager()->flush();

            $this->writeln('Storing incoming message with subject "' . substr($parser->getSubject(), 7) . '"');
        }
    }
}
