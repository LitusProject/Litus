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
 * The script to cache the xml of Ulyssis
 *
 * Usage:
 * --run|-r      Run the script
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// Setup autoloading
include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'run|r' => 'Run the script',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $cacheDir = $em->getRepository('CommonBundle\Entity\General\Config')
        ->getConfigValue('sport.cache_xml_path');

    if (!file_exists($cacheDir))
        mkdir($cacheDir);

    while (true) {
        $now = new \DateTime();
        $url = $em
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.run_result_page');
        $opts = array('http' =>
            array(
                'timeout' => 0.5,
            )
        );
        $fileContents = @file_get_contents($url, false, stream_context_create($opts));
        if (false !== $fileContents) {
            echo '[' . $now->format('d/m/Y H:i:s') . '] XML cached' . PHP_EOL;
            file_put_contents($cacheDir . 'ulyssis.xml', $fileContents);
        } else {
            echo '[' . $now->format('d/m/Y H:i:s') . '] XML failed' . PHP_EOL;
        }
        sleep(10);
    }
}
