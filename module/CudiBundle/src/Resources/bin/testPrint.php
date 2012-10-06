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

/**
 * The socket server for the sale queue
 *
 * Usage:
 * --run|-r      Run the socket
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// Setup autoloading
include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'printer|p-s' => 'Printer name',
    'ticket|t-s' => 'Ticket type',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->p) && isset($opts->t)) {
    $printers = unserialize(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.printers')
    );

    if (!isset($printers[$opts->p])) {
        echo 'Invalid printer name: ' . $opts->p . PHP_EOL;
        exit;
    }

    switch($opts->t) {
        case 'signin':
            $type = 1;
            $data = 's0202187##1234567890##3##63,00##Fundamentals of Computer Graphics#De Bijbel##45,00#8,00';
            break;
        case 'collect':
            $type = 2;
            $data = 's0202187##1234567890##3##63,00##Fundamentals of Computer Graphics#De Bijbel##45,00#8,00';
            break;
        case 'sale':
            $type = 2;
            $data = 's0202187##1234567890##3##63,00##Fundamentals of Computer Graphics#De Bijbel##45,00#8,00';
            break;
        default:
            echo 'Invalid ticket type: ' . $opts->t . PHP_EOL;
            exit(2);
    }

    $data = 'PRINT ' . $printers[$opts->p] . ' ' . $type . ' ' . $data;

    $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
    socket_connect(
        $socket,
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.print_socket_address'),
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.print_socket_port')
    );
    socket_write($socket, $data);
    socket_close($socket);
} else {
    echo 'Invalid options' . PHP_EOL;
    echo $opts->getUsageMessage();
    exit;
}
