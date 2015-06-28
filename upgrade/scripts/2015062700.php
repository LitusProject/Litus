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

$dumpFileName = '/tmp/syllabus_update_2015062700.txt';

$studies = array();
$studiesAcademicYearsMap = array();
$studiesGroupMap = array();
$studiesSubjectsMap = array();
$studyEnrollment = array();
$cvEntries = array();

// Get a local dump of the updated tables
echo ' -> Get a local dump of the tables' . PHP_EOL;
$usedStudies = array();
if (!file_exists($dumpFileName) || true) {
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studies['_' . $row['id']] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_academic_years_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studiesAcademicYearsMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT id, study_id, group_id, academic_year FROM syllabus.studies_group_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studiesGroupMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_subjects_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studiesSubjectsMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM users.study_enrollment');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studyEnrollment[] = $row;
    }
    $result = pg_query($connection, 'SELECT id, study FROM br.cv_entries');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $usedStudies[] = $row['study'];
        $cvEntries[] = $row;
    }

    $dump = serialize(
        array(
            'studies' => $studies,
            'studies_academic_years_map' => $studiesAcademicYearsMap,
            'studies_group_map' => $studiesGroupMap,
            'studies_subjects_map' => $studiesSubjectsMap,
            'study_enrollment' => $studyEnrollment,
            'cv_entries' => $cvEntries,
            'used_studies' => array_unique($usedStudies),
        )
    );

    $dumpFile = fopen($dumpFileName, 'w');
    fwrite($dumpFile, $dump);
    fclose($dumpFile);
} else {
    $dumpFile = fopen($dumpFileName, 'r');
    $dump = unserialize(fread($dumpFile, filesize($dumpFileName)));
    fclose($dumpFile);

    $studies = $dump['studies'];
    $studiesAcademicYearsMap = $dump['studies_academic_years_map'];
    $studiesGroupMap = $dump['studies_group_map'];
    $studiesSubjectsMap = $dump['studies_subjects_map'];
    $studyEnrollment = $dump['study_enrollment'];
    $cvEntries = $dump['cv_entries'];
    $usedStudies = $dump['used_studies'];
}

// Clear these tables (or columns)
// echo ' -> Clear all tables that will be updated' . PHP_EOL;
// pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_academic_years_map');
// pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_group_map');
// pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_subjects_map');
// pg_query($connection, 'DELETE FROM users.study_enrollment');
// pg_query($connection, 'DELETE FROM cudi.sales_articles_restrictions_study_map');
// pg_query($connection, 'DELETE FROM cudi.sales_session_restrictions_study_map');
// pg_query($connection, 'UPDATE br.cv_entries SET study = NULL');
// pg_query($connection, 'DELETE FROM syllabus.studies');

// Build the new syllabus structure
echo ' -> Build new syllabus structure' . PHP_EOL;
// exec('./bin/litus.sh orm:schema-tool:update --force', $output, $returnValue);
//
// if ($returnValue !== 0) {
//     echo ' Failed to update database, please try it manualy. This script can be run again afterwards.' . PHP_EOL;
//     exit(1);
// }

echo ' -> Migrate studies' . PHP_EOL;
// Merge duplicate studies
function sortOnKulId($item1, $item2)
{
    if ($item1['kul_id'] == $item2['kul_id']) {
        return 0;
    }

    return ($item1['kul_id'] < $item2['kul_id']) ? 1 : -1;
}
function searchOnKulId($studyDuplicates, $kulId, $phase)
{
    foreach ($studyDuplicates as $key => $data) {
        if ($data['study']['kul_id'] == $kulId && $data['study']['phase'] == $phase) {
            return $key;
        }
        if (!isset($data['study'])) {
            var_dump($data);
            exit;
        }
    }

    return -1;
}
function searchOnTitle($studyDuplicates, $title, $phase)
{
    foreach ($studyDuplicates as $key => $data) {
        if ($data['study']['title'] == $title && $data['study']['phase'] == $phase) {
            return $key;
        }
    }

    return -1;
}
$studiesSortedOnKulId = $studies;
usort($studiesSortedOnKulId, 'sortOnKulId');
$studyDuplicates = array();
foreach ($studiesSortedOnKulId as $study) {
    if ($study['kul_id'] === NULL) {
        $index = searchOnTitle($studyDuplicates, $study['title'], $study['phase']);
        if ($index == -1) {
            if (in_array($study['id'], $usedStudies)) {
                echo 'No study found with title ' . $study['title'] . '(' . $study['phase'] . ')' . PHP_EOL;
            }
        } else {
            $studyDuplicates[$index]['duplicates'][] = $study['id'];
        }
    } else {
        $index = searchOnKulId($studyDuplicates, $study['kul_id'], $study['phase']);
        if ($index == -1) {
            $studyDuplicates['_' . $study['id'] . '_' . $study['phase']] = array(
                'study' => $study,
                'duplicates' => array(
                    $study['id'],
                ),
            );
        } else {
            $studyDuplicates[$index]['duplicates'][] = $study['id'];
        }
    }
}

function createStudyFullTitle($data)
{
    global $studies;

    $title = '';
    if ($data['parent']) {
        $title = createStudyFullTitle($studies['_' . $data['parent']]) . ' - ';
    }

    return trim(preg_replace('/\s\s+/', ' ', $title . $data['title']));
}

foreach ($studyDuplicates as $data) {
    echo createStudyFullTitle($data['study']) . PHP_EOL;
}

// function createModuleGroup($data, $studyData, $parent = NULL)
// {
//     global $entityManager;
//     global $addedStudies;
//
//     if (in_array('id_' . $studyData['data'][0], $addedStudies) || (in_array('ext_' . $studyData['data'][5], $addedStudies) && NULL !== $studyData['data'][5]))
//         return;
//
//     $moduleGroup = new \SyllabusBundle\Entity\Study\ModuleGroup();
//     $moduleGroup->setExternalId($studyData['data'][5])
//         ->setTitle($studyData['data'][2])
//         ->setPhase($studyData['data'][3])
//         ->setLanguage($studyData['data'][4])
//         ->setMandatory(false)
//         ->setParent($parent);
//     $entityManager->persist($moduleGroup);
//
//     $addedStudies[] = 'id_' . $studyData['data'][0];
//     if (NULL !== $studyData['data'][5]) {
//         $addedStudies[] = 'ext_' . $studyData['data'][5];
//     }
//
//     foreach ($studyData['children'] as $study) {
//         createModuleGroup($data, $data['_' . $study], $moduleGroup);
//     }
// }
//
// foreach ($sortedStudies as $study) {
//     createModuleGroup($sortedStudies, $study, NULL);
// }
//
// $entityManager->flush();
//
// $groups = $entityManager->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
//     ->findAll();
//
// $parentIds = array();
// foreach ($groups as $group) {
//     if ($group->getParent()) {
//         $parentIds[] = $group->getParent()->getId();
//     }
// }
//
// $query = $entityManager->createQueryBuilder();
// $groups = $query->select('m')
//     ->from('SyllabusBundle\Entity\Study\ModuleGroup', 'm')
//     ->where(
//         $query->expr()->notIn('m.id', $parentIds)
//     )
//     ->orderBy('m.title')
//     ->getQuery()
//     ->getResult();
//
// function buildCombinationName($group)
// {
//     if (NULL == $group->getParent()) {
//         return $group->getTitle();
//     } else {
//         if (NULL == $group->getParent()->getParent()) {
//             return buildCombinationName($group->getParent()) . ': ' . $group->getTitle();
//         } else {
//             return buildCombinationName($group->getParent()) . ' - ' . $group->getTitle();
//         }
//     }
// }
//
// foreach($groups as $group) {
//     echo $group->getId() . ' - ' . buildCombinationName($group) . PHP_EOL;
// }
