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

/**
 * Assign an Ir.Reëel to all users who receive one at CuDi.
 *
 * Usage:
 * --flush|-f      Flush
 * --import|-i     The File
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
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
    'import|i-s' => 'Import File',
    'flush|f'    => 'Flush',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

foreach (file($opts->i) as $alias) {
    $alias = explode(':', trim($alias));

    $academic = $em->getRepository('CommonBundle\Entity\User\Person\Academic')
        ->findOneByEmail($alias[1]);

    if (null !== $academic) {
        echo 'Academic: ' . $academic->getFullName() . PHP_EOL;

        $newAlias = new MailBundle\Entity\Alias\Academic(
            strtolower($alias[0]), $academic
        );
    } else {
        echo 'External: ' . $alias[1] . PHP_EOL;

        $newAlias = new MailBundle\Entity\Alias\External(
            strtolower($alias[0]), strtolower($alias[1])
        );
    }

    $em->persist($newAlias);
}

if (isset($opts->f))
    $em->flush();
