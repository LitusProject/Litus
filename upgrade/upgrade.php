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

/**
 * To force an upgrade, use -f {number}
 */

$database = include __DIR__ . '/../config/database.config.php';

$connection = pg_connect(
    'dbname=' . $database['relational']['dbname']
    . ' host=' . $database['relational']['host']
    . ' port=' . $database['relational']['port']
    . ' user=' . $database['relational']['user']
    . ' password=' . $database['relational']['password']
);

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'last_upgrade\'');
if (pg_num_rows($result) == 0) {
    echo "\033[0;31mPlease run `php bin/console.php install:all` before attempting to upgrade\033[0;0m" . PHP_EOL;
    exit(1);
}

$result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'last_upgrade\'');
$lastUpgrade = pg_fetch_row($result)[0];

$files = array();
foreach (new DirectoryIterator(__DIR__ . '/scripts') as $fileInfo) {
    if ($fileInfo->isDot() || $fileInfo->getFilename() === 'README.md') {
        continue;
    }

    $files[] = $fileInfo->getFilename();
}

sort($files);

$options = getopt('f:');

// Run
include 'util.php';

$counter = 0;
foreach ($files as $file) {
    if ($file <= $lastUpgrade . '.php' && !(isset($options['f']) && $file == $options['f'] . '.php')) {
        continue;
    }

    $counter++;

    echo "Upgrade \033[0;33m" . substr($file, 0, strrpos($file, '.')) . "\033[0;0m" . PHP_EOL;
    include __DIR__ . '/scripts/' . $file;
}

if ($counter == 0) {
    echo "\033[0;32mThere were no new upgrades available\033[0;0m" . PHP_EOL;
}

pg_query($connection, 'UPDATE general.config SET value = \'' . substr($file, 0, strrpos($file, '.')) . '\' WHERE key = \'last_upgrade\'');
