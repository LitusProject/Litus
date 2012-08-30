<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

/**
 * Garbage collector for sessions
 *
 * Usage:
 * --all|-a        Run Garbage Collection
 * --sessions|s    Sessions Only
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// Setup autoloading
include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'all|a'         => 'Run Garbage Collection',
    'sessions|se'   => 'Sessions Only',
    'shibboleth|sh' => 'Shibboleth Only'
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->a) || isset($opts->se)) {
    echo 'Running Sessions GC...' . PHP_EOL;

    $sessions = $em->getRepository('CommonBundle\Entity\Users\Session')
        ->findAllExpired();

    foreach($sessions as $session)
        $em->remove($session);

    echo 'Removed ' . count($sessions) . ' expired sessions' . PHP_EOL;

    $em->flush();

    unset($session, $sessions);
}