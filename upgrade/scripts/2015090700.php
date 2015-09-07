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
$dumpFileName = '/tmp/units_update_2015090700.txt';
$unitMaps = array();
// Get a local dump of the updated tables
echo ' -> Get a local dump of the tables' . PHP_EOL;

if (!file_exists($dumpFileName)) {
    $result = pg_query($connection, 'SELECT * FROM users.people_organizations_unit_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $unitMaps[] = $row;
    }

    $dump = serialize(
        array(
            'unitMaps' => $unitMaps,
        )
    );
    $dumpFile = fopen($dumpFileName, 'w');
    fwrite($dumpFile, $dump);
    fclose($dumpFile);
} else {
    $dumpFile = fopen($dumpFileName, 'r');
    $dump = unserialize(fread($dumpFile, filesize($dumpFileName)));
    fclose($dumpFile);
    $unitMaps = $dump['unitMaps'];
}
// Clear these tables (or columns)
echo ' -> Clear all tables that will be updated' . PHP_EOL;
pg_query($connection, 'DROP TABLE IF EXISTS users.people_organizations_unit_map');

// Build the new units structure
echo ' -> Build new units structure' . PHP_EOL;
exec('./bin/litus.sh orm:schema-tool:update --force', $output, $returnValue);
if ($returnValue !== 0) {
    echo ' Failed to update database, please try it manualy. This script can be run again afterwards.' . PHP_EOL;
    exit(1);
}
echo ' -> Import old unitMaps into new structure' . PHP_EOL;
foreach ($unitMaps as $map) {
    $academicYear = $entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')
        ->findOneById($map['academic_year']);
    $academic = $entityManager->getRepository('CommonBundle\Entity\User\Person\Academic')
        ->findOneById($map['academic']);
    $unit = $entityManager->getRepository('CommonBundle\Entity\General\Organization\Unit')
        ->findOneById($map['unit']);
    $coordinator = $map['coordinator'];
    $unitMap = new \CommonBundle\Entity\User\Person\Organization\UnitMap\Academic($academic, $academicYear, $unit, $coordinator);
    $entityManager->persist($unitMap);
}
$entityManager->flush();
