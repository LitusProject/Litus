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

function addConfigKey($connection, $name, $value, $description)
{
    pg_query($connection, 'INSERT INTO general.config VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $description . '\')');
}

function getConfigValue($connection, $name)
{
    $result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'' . $name . '\'');
    if (0 == pg_num_rows($result))
        throw new \RuntimeException('The config key ' . $name . ' does not exist');

    return pg_fetch_row($result)[0];
}

function removeConfigKey($connection, $name)
{
    pg_query($connection, 'DELETE FROM general.config WHERE key = \'' . $name . '\'');
}

function renameConfigKey($connection, $oldName, $newName, $description = null)
{
    pg_query($connection, 'UPDATE general.config SET key = \'' . $newName . '\' WHERE key = \'' . $oldName . '\'');
    if (null !== $description)
        pg_query($connection, 'UPDATE general.config SET description = \'' . $description . '\' WHERE key = \'' . $newName . '\'');
}

function publishConfigValue($connection, $name)
{
    pg_query($connection, 'UPDATE general.config SET published = TRUE WHERE key = \'' . $name . '\'');
}

function updateConfigKey($connection, $name, $value)
{
    pg_query($connection, 'UPDATE general.config SET value = \'' . $value . '\' WHERE key = \'' . $name . '\'');
}

function removeAclAction($connection, $resource, $action)
{
    $result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'' . $resource . '\' AND name = \'' . $action . '\'');
    if (0 == pg_num_rows($result))
        throw new \RuntimeException('The ACL action ' . $resource . '.' . $action . ' does not exist');

    $id = pg_fetch_row($result)[0];

    pg_query($connection, 'DELETE FROM acl.roles_actions_map WHERE action = \'' . $id . '\'');
    pg_query($connection, 'DELETE FROM acl.actions WHERE id = \'' . $id . '\'');
}
