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
include 'init_autoloader.php';
$app = Zend\Mvc\Application::init(include 'config/application.config.php');
$entityManager = $app->getServiceManager()->get('doctrineormentitymanager');
$dumpFileName = '/tmp/cv_update_2015111500.txt';
$cvEntries = array();
// Get a local dump of the updated tables
echo ' -> Get a local dump of the tables' . PHP_EOL;

if (!file_exists($dumpFileName)) {
    $result = pg_query($connection, 'SELECT id, experiences FROM br.cv_entries');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $cvEntries[] = $row;
    }

    $dump = serialize(
        array(
            'cvEntries' => $cvEntries,
        )
    );
    $dumpFile = fopen($dumpFileName, 'w');
    fwrite($dumpFile, $dump);
    fclose($dumpFile);
} else {
    $dumpFile = fopen($dumpFileName, 'r');
    $dump = unserialize(fread($dumpFile, filesize($dumpFileName)));
    fclose($dumpFile);
    $cvEntries = $dump['cvEntries'];
}

// Build the new units structure
echo ' -> Import old experiences into new structure' . PHP_EOL;
foreach ($cvEntries as $entry) {
    $exp = $entry['experiences'];
    $cv = $entityManager->getRepository('BrBundle\Entity\Cv\Entry')
        ->findOneById($entry['id']);

    $experience = new \BrBundle\Entity\Cv\Experience($cv, $exp);
    $entityManager->persist($experience);
}
$entityManager->flush();
