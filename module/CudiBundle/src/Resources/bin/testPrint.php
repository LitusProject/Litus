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
 * Test the printer service.
 *
 * Usage:
 * --printer|-p     Printer Name
 * --ticket|-t      Ticket Type
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'printer|p-s' => 'Printer Name',
    'ticket|t-s' => 'Ticket Type',
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
        echo 'Possible printers:' . PHP_EOL;
        foreach($printers as $key => $printer)
            echo '    -> ' . $key . ':' . $printer . PHP_EOL;
        exit;
    }

    $data = (object) array(
        'id' => 's0210425',
        'barcode' => '1234567890',
        'name' => 'Kristof Mariën',
        'queuenumber' => '3',
        'totalAmount' => '63,00',
        'items' => array(
            array(
                'title' => 'Fundamentals of Computer Graphics',
                'price' => '45,00',
                'barcode' => '12345',
                'number' => '1',
            ),
            array(
                'title' => 'De Bijbel',
                'price' => '8,00',
                'barcode' => '54321',
                'number' => '2',
            ),
        )
    );
    switch($opts->t) {
        case 'signin':
            $data->type = 1;
            break;
        case 'collect':
            $data->type = 2;
            break;
        case 'sale':
            $data->type = 3;
            break;
        default:
            echo 'Invalid ticket type: ' . $opts->t . PHP_EOL;
            echo 'Possible printers:' . PHP_EOL;
            echo '    -> signin' . PHP_EOL;
            echo '    -> collect' . PHP_EOL;
            echo '    -> sale' . PHP_EOL;
            exit(2);
    }

    $data = json_encode(
        (object) array(
            'command' => 'PRINT',
            'id' => $printers[$opts->p],
            'ticket' => $data,
            'key' => $em->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_socket_key'),
        )
    );

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
