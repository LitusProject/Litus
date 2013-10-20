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
 * Cache the JSON of the official result page.
 *
 * Usage:
 * --run|-r      Cache
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'run|r' => 'Cache JSON',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    while (true) {
        $now = new \DateTime();
        $resultPage = $em
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.run_result_page');

        $options = array(
            'http' => array(
                'timeout' => 0.5,
            )
        );

        $fileContents = @file_get_contents($resultPage, false, stream_context_create($options));
        if (false !== $fileContents) {
            echo '[' . $now->format('d/m/Y H:i:s') . '] Succesfully cached the result page' . PHP_EOL;
            file_put_contents('data/cache/' . md5('run_result_page'), $fileContents);

            $resultPage = (array) json_decode($fileContents);
            sleep(substr($resultPage['update'], 0, strlen($resultPage['update'])-1));
        } else {
            echo '[' . $now->format('d/m/Y H:i:s') . '] Failed to cache the result page' . PHP_EOL;
            sleep(10);
        }
    }
}
