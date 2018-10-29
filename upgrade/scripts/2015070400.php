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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

ini_set('memory_limit', '500M');

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
if (!file_exists($dumpFileName)) {
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $studies['_' . $row['id']] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM users.study_enrollment');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $usedStudies[] = $row['study'];
        $studyEnrollment[] = $row;
    }
    $result = pg_query($connection, 'SELECT id, study FROM br.cv_entries');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $usedStudies[] = $row['study'];
        $cvEntries[] = $row;
    }

    $usedStudies = array_unique($usedStudies);

    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_academic_years_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        if (in_array($row['study'], $usedStudies)) {
            $studiesAcademicYearsMap[] = $row;
        }
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_group_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        if (in_array($row['study_id'], $usedStudies)) {
            $studiesGroupMap[] = $row;
        }
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_subjects_map');
    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        if (in_array($row['study_id'], $usedStudies)) {
            $studiesSubjectsMap[] = $row;
        }
    }

    $dump = serialize(
        array(
            'studies'                    => $studies,
            'studies_academic_years_map' => $studiesAcademicYearsMap,
            'studies_group_map'          => $studiesGroupMap,
            'studies_subjects_map'       => $studiesSubjectsMap,
            'study_enrollment'           => $studyEnrollment,
            'cv_entries'                 => $cvEntries,
            'used_studies'               => $usedStudies,
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
echo ' -> Clear all tables that will be updated' . PHP_EOL;
pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_academic_years_map');
pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_group_map');
pg_query($connection, 'DROP TABLE IF EXISTS syllabus.studies_subjects_map');
pg_query($connection, 'DELETE FROM users.study_enrollment');
pg_query($connection, 'DELETE FROM cudi.sales_articles_restrictions_study_map');
pg_query($connection, 'DELETE FROM cudi.sales_session_restrictions_study_map');
pg_query($connection, 'UPDATE br.cv_entries SET study = NULL');
pg_query($connection, 'DELETE FROM syllabus.studies');

// Build the new syllabus structure
echo ' -> Build new syllabus structure' . PHP_EOL;
exec('php bin/doctrine.php orm:schema-tool:update --force', $output, $returnValue);

if ($returnValue !== 0) {
    echo ' Failed to update database, please try it manualy. This script can be run again afterwards.' . PHP_EOL;
    exit(1);
}

echo ' -> Migrate studies' . PHP_EOL;

function createStudyFullTitle($data)
{
    global $studies;

    $title = '';
    if ($data['parent']) {
        $title = createStudyFullTitle($studies['_' . $data['parent']]) . ' - ';
    }

    return trim(preg_replace('/\s\s+/', ' ', $title . $data['title']));
}

$addedModuleGroups = array();
foreach ($usedStudies as $id) {
    $studyData = $studies['_' . $id];
    $key = createStudyFullTitle($studyData) . '_' . $studyData['phase'];

    echo '   -> Add \'' . $key . '\'' . PHP_EOL;

    if (isset($addedModuleGroups[$key])) {
        $moduleGroup = $addedModuleGroups[$key];
    } else {
        $moduleGroup = new \SyllabusBundle\Entity\Study\ModuleGroup();
        $moduleGroup->setExternalId(null)
            ->setTitle(createStudyFullTitle($studies['_' . $id]))
            ->setPhase($studies['_' . $id]['phase'])
            ->setLanguage($studies['_' . $id]['language'])
            ->setMandatory(false);
        $entityManager->persist($moduleGroup);

        $addedModuleGroups[$key] = $moduleGroup;
    }

    $combination = new \SyllabusBundle\Entity\Study\Combination();
    $combination->setExternalId(null)
        ->setTitle(createStudyFullTitle($studies['_' . $id]))
        ->setPhase($studies['_' . $id]['phase'])
        ->setModuleGroups(array($moduleGroup));
    $entityManager->persist($combination);

    foreach ($studiesAcademicYearsMap as $mapKey => $map) {
        if ($map['study'] == $id) {
            $academicYear = $entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneById($map['academic_year']);
            unset($studiesAcademicYearsMap[$mapKey]);
            break;
        } else {
            $newStudiesAcademicYearsMap[] = $map;
        }
    }
    $studiesAcademicYearsMap = array_values($studiesAcademicYearsMap);

    $study = new \SyllabusBundle\Entity\Study();
    $study->setCombination($combination)
        ->setAcademicYear($academicYear);
    $entityManager->persist($study);

    $newStudiesGroupMap = array();
    foreach ($studiesGroupMap as $map) {
        if ($map['study_id'] == $id) {
            $group = $entityManager->getRepository('SyllabusBundle\Entity\Group')
                ->findOneById($map['group_id']);

            $studyMap = new \SyllabusBundle\Entity\Group\StudyMap($study, $group);

            $entityManager->persist($studyMap);
        } else {
            $newStudiesGroupMap[] = $map;
        }
    }
    $studiesGroupMap = $newStudiesGroupMap;

    $newStudiesSubjectsMap = array();
    foreach ($studiesSubjectsMap as $map) {
        if ($map['study_id'] == $id) {
            $subject = $entityManager->getRepository('SyllabusBundle\Entity\Subject')
                ->findOneById($map['subject_id']);

            $academicYear = $entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneById($map['academic_year']);

            $subjectMap = new \SyllabusBundle\Entity\Study\SubjectMap($moduleGroup, $subject, $map['mandatory'] == 't', $academicYear);

            $entityManager->persist($subjectMap);
        } else {
            $newStudiesSubjectsMap[] = $map;
        }
    }
    $studiesSubjectsMap = $newStudiesSubjectsMap;

    $newStudyEnrollment = array();
    foreach ($studyEnrollment as $map) {
        if ($map['study'] == $id) {
            $academic = $entityManager->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($map['academic']);

            $entrollment = new \SecretaryBundle\Entity\Syllabus\StudyEnrollment($academic, $study);

            $entityManager->persist($entrollment);
        } else {
            $newStudyEnrollment[] = $map;
        }
    }
    $studyEnrollment = $newStudyEnrollment;

    $newCvEntries = array();
    foreach ($cvEntries as $map) {
        if ($map['study'] == $id) {
            $entry = $entityManager->getRepository('BrBundle\Entity\Cv\Entry')
                ->findOneById($map['id']);

            $entry->setStudy($study);
        } else {
            $newCvEntries[] = $map;
        }
    }
    $cvEntries = $newCvEntries;
}

$entityManager->flush();
