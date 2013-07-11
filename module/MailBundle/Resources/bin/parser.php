<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

/**
 * Parser for received e-mail messages.
 *
 * Usage:
 * --run|-r      Run the Parser
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$dm = $application->getServiceManager()->get('doctrine.documentmanager.odm_default');

if ('production' == getenv('APPLICATION_ENV'))
    $lilo = $application->getServiceManager()->get('lilo');

$rules = array(
    'run|r' => 'Run the Parser',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $stdinStream = fopen('php://stdin', 'r');
    $message = '';
    while (!feof($stdinStream)) {
        $line = fread($stdinStream, 1024);
        $message .= $line;
    }
    fclose($stdinStream);

    $parser = new MailBundle\Component\Parser\Message($message);
    $commands = array(
        '001' => 'Store the incoming mail and its attachments...'
    );

    $command = substr($parser->getSubject(), 2, 3);
    if (in_array($command, array_keys($commands))) {
        switch ($command) {
            case '001':
                $attachments = array();
                foreach ($parser->getAttachments() as $attachment) {
                    $attachments[] = new MailBundle\Document\Message\Attachment(
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
                    $newMessage = new MailBundle\Document\Message(
                        $body['type'],
                        substr($parser->getSubject(), 7),
                        $body['content'],
                        $attachments
                    );

                    $dm->persist($newMessage);
                    $dm->flush();

                    if ('production' == getenv('APPLICATION_ENV')) {
                        $lilo->sendLog(
                            'Storing incoming message with subject "' . substr($parser->getSubject(), 7) . '"',
                            array(
                                'MailBundle',
                                'parser.php'
                            )
                        );
                    }
                }
            break;
            default:
                if ('production' == getenv('APPLICATION_ENV')) {
                    $lilo->sendLog(
                        'Invalid command specified in the subject line (' . $command . ')',
                        array(
                            'MailBundle',
                            'parser.php'
                        )
                    );
                }
            break;
        }
    }
}
