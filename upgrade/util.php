<?php

function renameConfigKey($connection, $oldName, $newName)
{
    $database = include __DIR__ . '/../config/database.config.php';

    $connection = pg_connect(
        'dbname=' . $database['relational']['dbname']
        . ' host=' . $database['relational']['host']
        . ' port=' . $database['relational']['port']
        . ' user=' . $database['relational']['user']
        . ' password=' . $database['relational']['password']
    );

    $result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'' . $oldName . '\'');
    if (0 == pg_num_rows($result))
        throw new \RuntimeException('The config key ' . $oldName . ' does not exist');

    $oldValue = pg_fetch_row($result)[0];

    $result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'' . $newName . '\'');
    if (0 == pg_num_rows($result))
        throw new \RuntimeException('The config key ' . $newName . ' does not yet exist');

    pg_query($connection, 'UPDATE general.config SET value = \'' . $oldValue . '\' WHERE key = \'' . $newName . '\'');
    pg_query($connection, 'DELETE FROM general.config WHERE key = \'' . $oldName . '\'');
}