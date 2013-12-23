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
 * The socket server for the syllabus update.
 *
 * Usage:
 * --run|-r       Run the Socket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');
$mt = $application->getServiceManager()->get('mail_transport');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

$rules = array(
    'run|r' => 'Run the Socket',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $update = new \SyllabusBundle\Component\WebSocket\Update($em, $mt);
    $update->process();
}
