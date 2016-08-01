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
 * A script to assign the studies to groups
 *
 * Usage:
 * --run|-r       Run the Script
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
    'run|r' => 'Run the Script',
    'flush|f' => 'Flush',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

$filters = array(
    // Afstuderende Masters (Behalve Architectuur)
    '1' => array(
        'and' => array('master'),
        'or' => array(),
        'not' => array('architectuur'),
        'phases' => array(2),
    ),
    // Master Computerwetenschappen
    '2' => array(
        'and' => array('master'),
        'or' => array('computer science', 'computerwetenschappen'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Architectuur
    '3' => array(
        'and' => array('master', 'architectuur'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Werktuigkunde
    '4' => array(
        'and' => array('master', 'werktuigkunde'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Chemie
    '5' => array(
        'and' => array('master'),
        'or' => array('chemische', 'chemical'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Biomedische Technologie
    '6' => array(
        'and' => array('master'),
        'or' => array('biomedische', 'biomedical'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Energie
    '7' => array(
        'and' => array('master'),
        'or' => array('energie', 'energy'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Verkeer, Logistiek en Intelligente Transportsystemen
    '8' => array(
        'and' => array('master', 'verkeer'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Wiskundige Ingenieurstechnieken
    '9' => array(
        'and' => array('master', 'wiskundige'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Elektrotechniek
    '10' => array(
        'and' => array('master'),
        'or' => array('electrical', 'elektrotechniek'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Materiaalkunde
    '11' => array(
        'and' => array('master'),
        'or' => array('materiaalkunde', 'materials', 'nanoscience'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Bouwkunde
    '12' => array(
        'and' => array('master', 'bouwkunde'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Nanowetenschappen en Nanotechnologie
    '13' => array(
        'and' => array('master'),
        'or' => array('nanowetenschappen', 'nanoscience'),
        'not' => array(),
        'phases' => array(),
    ),
    // Master Artificiële Intelligentie
    '14' => array(
        'and' => array('Master of Artificial Intelligence'),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Afstuderende Masters (Architectuur)
    '16' => array(
        'and' => array('master', 'architectuur'),
        'or' => array(),
        'not' => array(),
        'phases' => array(2),
    ),
    // Derde Bachelor
    '17' => array(
        'and' => array('bachelor'),
        'or' => array(),
        'not' => array(),
        'phases' => array(3),
    ),
    // Derde Bachelor Computerwetenschappen
    '18' => array(
        'and' => array('bachelor', 'computerwetenschappen'),
        'or' => array(),
        'not' => array(),
        'phases' => array(3),
    ),
    // Eerste Master Computerwetenschappen
    '19' => array(
        'and' => array('master'),
        'or' => array('computer science', 'computerwetenschappen'),
        'not' => array(),
        'phases' => array(1),
    ),
    // Extra Members
    '20' => array(
        'and' => array(),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Vergeten groepen (tijdelijk)
    '21' => array(
        'and' => array(),
        'or' => array(),
        'not' => array(),
        'phases' => array(),
    ),
    // Bouwkunde (bachelor en master)
    '22' => array(
        'and' => array('bouwkunde'),
        'or' => array('bachelor', 'master'),
        'not' => array(),
        'phases' => array(),
    ),
);

if (isset($opts->r)) {
    $academicYear = \CommonBundle\Component\Util\AcademicYear::getUniversityYear($em);

    $studies = $em->getRepository('SyllabusBundle\Entity\AcademicYearMap')
        ->findAllByAcademicYear($academicYear);

    foreach($filters as $id => $filter) {
        $group = $em->getRepository('SyllabusBundle\Entity\Group')
            ->findOneById($id);

        echo '+ ' . $group->getName() . PHP_EOL;

        $mappings = $em->getRepository('SyllabusBundle\Entity\StudyGroupMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        foreach($mappings as $mapping)
            $em->remove($mapping);

        if ($opts->f)
            $em->flush();

        foreach($studies as $study) {
            $match = true;
            foreach($filter['and'] as $item) {
                if (strpos(strtolower($study->getStudy()->getFullTitle()), strtolower($item)) === false)
                    $match = false;
            }
            if (count($filter['or']) > 0) {
                $oneFound = false;
                foreach($filter['or'] as $item) {
                    if (strpos(strtolower($study->getStudy()->getFullTitle()), strtolower($item)) !== false)
                        $oneFound = true;
                }
                $match = $oneFound ? $match : false;
            }
            foreach($filter['not'] as $item) {
                if (strpos(strtolower($study->getStudy()->getFullTitle()), strtolower($item)) !== false)
                    $match = false;
            }
            if (count($filter['phases']) > 0) {
                if (!in_array($study->getStudy()->getPhase(), $filter['phases']))
                    $match = false;
            }
            if ($match) {
                $em->persist(new \SyllabusBundle\Entity\StudyGroupMap($study->getStudy(), $group, $academicYear));
                echo '  - (Phase ' . $study->getStudy()->getPhase() . ') ' . $study->getStudy()->getFullTitle() . PHP_EOL;
            }
        }
    }

    if ($opts->f)
        $em->flush();
}