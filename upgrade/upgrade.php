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

$database = include __DIR__ . '/../config/database.config.php';

$connection = pg_connect(
    'dbname=' . $database['relational']['dbname']
    . ' host=' . $database['relational']['host']
    . ' port=' . $database['relational']['port']
    . ' user=' . $database['relational']['user']
    . ' password=' . $database['relational']['password']
);

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'upgrade_date\'');

if (pg_num_rows($result) == 0)
    pg_query($connection, 'INSERT INTO general.config VALUES(\'upgrade_date\', \'0\', \'The date the last upgrade was done\')');

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'upgrade_date\'');
$config = pg_fetch_row($result)[0];

foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
    if($fileInfo->isDot() || 'upgrade.php' == $fileInfo->getFilename())
        continue;

    $files[] = $fileInfo->getFilename();
}

sort($files);

foreach($files as $file) {
    if ($file <= $config . '.php')
        continue;

    echo 'Upgrade ' . substr($file, 0, strrpos($file, '.')) . PHP_EOL;
    include __DIR__ . '/' . $file;
}

pg_query($connection, 'UPDATE general.config SET value = \'' . substr($file, 0, strrpos($file, '.')) . '\' WHERE key = \'upgrade_date\'');
