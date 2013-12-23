<?php

function renameConfigKey($connection, $oldName, $newName, $description = null)
{
    pg_query($connection, 'UPDATE general.config SET key = \'' . $newName . '\' WHERE key = \'' . $oldName . '\'');
    if (null !== $description)
        pg_query($connection, 'UPDATE general.config SET description = \'' . $description . '\' WHERE key = \'' . $newName . '\'');
}

function getConfigValue($connection, $name)
{
    $result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'' . $name . '\'');
    if (0 == pg_num_rows($result))
        throw new \RuntimeException('The config key ' . $name . ' does not exist');

    return pg_fetch_row($result)[0];
}

function addConfigKey($connection, $name, $value, $description)
{
    pg_query($connection, 'INSERT INTO general.config VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $description . '\')');
}

function removeConfigKey($connection, $name)
{
    pg_query($connection, 'DELETE FROM general.config WHERE key = \'' . $name . '\'');
}