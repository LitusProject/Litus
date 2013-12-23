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
 * Garbage collector for sessions.
 *
 * Usage:
 * --all|-a        Run Garbage Collection
 * --sessions|s    Sessions Only
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

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

    $sessions = $em->getRepository('CommonBundle\Entity\User\Session')
        ->findAllExpired();

    foreach($sessions as $session)
        $em->remove($session);

    echo 'Removed ' . count($sessions) . ' expired sessions' . PHP_EOL;

    $em->flush();

    unset($session, $sessions);
}

if (isset($opts->a) || isset($opts->sh)) {
    echo 'Running Shibboleth GC...' . PHP_EOL;

    $sessions = $em->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
        ->findAllExpired();

    foreach($sessions as $session)
        $em->remove($session);

    echo 'Removed ' . count($sessions) . ' expired Shibboleth codes' . PHP_EOL;

    $em->flush();

    unset($session, $sessions);
}
