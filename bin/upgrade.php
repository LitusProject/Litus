<?php

$database = include '../config/database.config.php';

$connection = pg_connect('dbname=' . $database['relational']['dbname'] . '
                host=' . $database['relational']['host'] . '
                port=' . $database['relational']['port'] . '
                user=' . $database['relational']['user'] . '
                password=' . $database['relational']['password']);

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'upgrade_date\'');

if (pg_num_rows($result) == 0) {
    pg_query($connection, 'INSERT INTO general.config VALUES(\'upgrade_date\', \'0\', \'The date the last upgrade was done\')');
}

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'upgrade_date\'');

$config = pg_fetch_row($result)[0];

$handle = opendir('upgrade/');
$files = array();
while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != "..") {
        $files[] = $entry;
    }
}

sort($files);

foreach($files as $file) {
    if ($file <= $config . '.php')
        continue;

    echo 'Upgrade ' . substr($file, 0, strrpos($file, '.')) . PHP_EOL;
    include 'upgrade/' . $file;
}

pg_query($connection, 'UPDATE general.config SET value = \'' . substr($file, 0, strrpos($file, '.')) . '\' WHERE key = \'upgrade_date\'');
