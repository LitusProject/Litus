<?php

pg_query($connection, 'ALTER TABLE publications.editions ADD file_name VARCHAR(255)');

$academicYears = array();

// Update PDF
$result = pg_query($connection, 'SELECT E.title AS title, PUB.title AS pubtitle, E.id AS id, A.start AS start
    FROM publications.editions_pdf AS P
    INNER JOIN publications.editions AS E ON E.id = P.id
    INNER JOIN general.academic_years AS A ON E.academic_year = A.id
    INNER JOIN publications.publications AS PUB ON PUB.id = E.publication');

$pdfDir = 'public/_publications/pdf';
if (!file_exists($pdfDir))
    mkdir($pdfDir, 0775, true);

while($edition = pg_fetch_object($result)) {
    $startAcademicYear = new DateTime($edition->start);
    $academicYears[$startAcademicYear->format('y')] = $startAcademicYear->format('y');
    $oldFileName = 'public/_publications/' . $startAcademicYear->format('y') . ($startAcademicYear->format('y') + 1) .
        '/pdf/' . createSlug($edition->pubtitle) . '/' . createSlug($edition->title) . '.pdf';
    $oldFileName = str_replace('/irreeel', '/irreel', $oldFileName);

    $fileName = '';
    do{
        $fileName = sha1(uniqid()) . '.pdf';
    } while (file_exists($pdfDir . '/' . $fileName));
    $newFileName = $pdfDir . '/' . $fileName;

    pg_query($connection, 'UPDATE publications.editions SET file_name = \'' . $fileName . '\' WHERE id = ' . $edition->id);
    rename($oldFileName, $newFileName);
}

// Update HTML
$result = pg_query($connection, 'SELECT P.html AS html, E.title AS title, PUB.title AS pubtitle, E.id AS id, PUB.id AS pubid, A.start AS start
    FROM publications.editions_html AS P
    INNER JOIN publications.editions AS E ON E.id = P.id
    INNER JOIN general.academic_years AS A ON E.academic_year = A.id
    INNER JOIN publications.publications AS PUB ON PUB.id = E.publication');

$htmlDir = 'public/_publications/html';
if (!file_exists($htmlDir))
    mkdir($htmlDir, 0775, true);

while($edition = pg_fetch_object($result)) {
    $startAcademicYear = new DateTime($edition->start);
    $academicYears[$startAcademicYear->format('y')] = $startAcademicYear->format('y');
    $oldFileName = 'public/_publications/' . $startAcademicYear->format('y') . ($startAcademicYear->format('y') + 1) .
        '/html/' . createSlug($edition->pubtitle) . '/' . createSlug($edition->title);

    $fileName = '';
    do{
        $fileName = sha1(uniqid());
    } while (file_exists($htmlDir . '/' . $fileName));
    $newFileName = $htmlDir . '/' . $fileName;

    pg_query($connection, 'UPDATE publications.editions SET file_name = \'' . $fileName . '\' WHERE id = ' . $edition->id);

    $html = str_replace(str_replace('public/', '', $oldFileName), str_replace('public/', '', $htmlDir) . '/' . $fileName, $edition->html);
    preg_match('/_publications\/([0-9]{4})\/pdf\/het-bakske\/.*(s[0-9]+w[0-9]+)\.pdf/', $html, $matches);
    if (isset($matches[1]) && isset($matches[2])) {
        $pdfResult = pg_query($connection, 'SELECT *
            FROM publications.editions_pdf AS P
            INNER JOIN publications.editions AS E ON E.id = P.id
            INNER JOIN general.academic_years AS A ON E.academic_year = A.id
            WHERE E.publication = ' . $edition->pubid . ' AND lower(E.title) = \'' . $matches[2] . '\'');

        while($row = pg_fetch_object($pdfResult)) {
            if (strpos($row->start, '20' . substr($matches[1], 0, 2)) !== false) {
                $html = preg_replace('/_publications\/[0-9]{4}\/pdf\/het-bakske\/.*s[0-9]+w[0-9]+\.pdf/', str_replace('public/', '', $pdfDir) . '/' . $row->file_name, $html);
            }
        }
    }
    pg_query($connection, "UPDATE publications.editions_html SET html = '" . pg_escape_string($html) . "' WHERE id = " . $edition->id);
    rename($oldFileName, $newFileName);
}

foreach($academicYears as $year) {
    rrmdir('public/_publications/' . $year . ($year + 1));
}

addConfigKey($connection, 'publication.public_pdf_directory', '/_publications/pdf/', 'The public pdf direction of publication');
addConfigKey($connection, 'publication.public_html_directory', '/_publications/html/', 'The public html direction of publication');

if(!function_exists("createSlug")) {
    function createSlug($string, $delimiter = '-') {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }
}

if(!function_exists("rrmdir")) {
    function rrmdir($dir)
    {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }
}