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

$dumpFileName = '/tmp/syllabus_update_2015062700.txt';

$studies = array();
$studiesAcademicYearsMap = array();
$studiesGroupMap = array();
$studiesSubjectsMap = array();
$studyEnrollment = array();
$salesArticlesRestrictionsStudyMap = array();
$salesSessionRestrictionsStudyMap = array();
$cvEntries = array();

// Get a local dump of the updated tables
echo ' -> Get a local dump of the tables' . PHP_EOL;
if (!file_exists($dumpFileName)) {
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies');
    while ($row = pg_fetch_row($result)) {
        $studies[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_academic_years_map');
    while ($row = pg_fetch_row($result)) {
        $studiesAcademicYearsMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_group_map');
    while ($row = pg_fetch_row($result)) {
        $studiesGroupMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM syllabus.studies_subjects_map');
    while ($row = pg_fetch_row($result)) {
        $studiesSubjectsMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM users.study_enrollment');
    while ($row = pg_fetch_row($result)) {
        $studyEnrollment[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM cudi.sales_articles_restrictions_study_map');
    while ($row = pg_fetch_row($result)) {
        $salesArticlesRestrictionsStudyMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM cudi.sales_session_restrictions_study_map');
    while ($row = pg_fetch_row($result)) {
        $salesSessionRestrictionsStudyMap[] = $row;
    }
    $result = pg_query($connection, 'SELECT * FROM br.cv_entries');
    while ($row = pg_fetch_row($result)) {
        $cvEntries[] = $row;
    }

    $dump = serialize(
        array(
            'studies' => $studies,
            'studies_academic_years_map' => $studiesAcademicYearsMap,
            'studies_group_map' => $studiesGroupMap,
            'studies_subjects_map' => $studiesSubjectsMap,
            'study_enrollment' => $studyEnrollment,
            'sales_articles_restrictions_study_map' => $salesArticlesRestrictionsStudyMap,
            'sales_session_restrictions_study_map' => $salesSessionRestrictionsStudyMap,
            'cv_entries' => $cvEntries,
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
    $salesArticlesRestrictionsStudyMap = $dump['sales_articles_restrictions_study_map'];
    $salesSessionRestrictionsStudyMap = $dump['sales_session_restrictions_study_map'];
    $cvEntries = $dump['cv_entries'];
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
exec('./bin/litus.sh orm:schema-tool:update --force', $output, $returnValue);

if ($returnValue !== 0) {
    echo ' Failed to update database, please try it manualy. This script can be run again afterwards.' . PHP_EOL;
    exit(1);
}
