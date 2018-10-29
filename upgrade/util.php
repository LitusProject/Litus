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
 * @param  resource $connection
 * @param  string   $name
 * @param  string   $value
 * @param  string   $description
 * @return null
 */
function addConfigKey($connection, $name, $value, $description)
{
    pg_query($connection, 'INSERT INTO general.config VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $description . '\')');
}

/**
 * @param  resource $connection
 * @param  string   $name
 * @return string
 */
function getConfigValue($connection, $name)
{
    $result = pg_query($connection, 'SELECT value FROM general.config WHERE key = \'' . $name . '\'');
    if (pg_num_rows($result) == 0) {
        throw new \RuntimeException('The config key ' . $name . ' does not exist');
    }

    return pg_fetch_row($result)[0];
}

/**
 * @param  resource $connection
 * @param  string   $name
 * @return null
 */
function removeConfigKey($connection, $name)
{
    pg_query($connection, 'DELETE FROM general.config WHERE key = \'' . $name . '\'');
}

/**
 * @param  resource    $connection
 * @param  string      $oldName
 * @param  string      $newName
 * @param  string|null $description
 * @return null
 */
function renameConfigKey($connection, $oldName, $newName, $description = null)
{
    pg_query($connection, 'UPDATE general.config SET key = \'' . $newName . '\' WHERE key = \'' . $oldName . '\'');
    if ($description !== null) {
        pg_query($connection, 'UPDATE general.config SET description = \'' . $description . '\' WHERE key = \'' . $newName . '\'');
    }
}

/**
 * @param  resource    $connection
 * @param  string      $name
 * @param  string      $newValue
 * @param  string|null $description
 * @return null
 */
function updateConfigValue($connection, $name, $newValue, $description = null)
{
    pg_query($connection, 'UPDATE general.config SET value = \'' . $newValue . '\' WHERE key = \'' . $name . '\'');
    if ($description !== null) {
        pg_query($connection, 'UPDATE general.config SET description = \'' . $description . '\' WHERE key = \'' . $name . '\'');
    }
}

/**
 * @param  resource $connection
 * @param  string   $name
 * @return null
 */
function publishConfigValue($connection, $name)
{
    pg_query($connection, 'UPDATE general.config SET published = TRUE WHERE key = \'' . $name . '\'');
}

/**
 * @param  resource $connection
 * @param  string   $name
 * @param  string   $value
 * @return null
 */
function updateConfigKey($connection, $name, $value)
{
    pg_query($connection, 'UPDATE general.config SET value = \'' . $value . '\' WHERE key = \'' . $name . '\'');
}

/**
 * @param  resource $connection
 * @param  string   $resource
 * @param  string   $action
 * @return null
 */
function removeAclAction($connection, $resource, $action)
{
    $result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'' . $resource . '\' AND name = \'' . $action . '\'');
    if (pg_num_rows($result) == 0) {
        throw new \RuntimeException('The ACL action ' . $resource . '.' . $action . ' does not exist');
    }

    $id = pg_fetch_row($result)[0];

    pg_query($connection, 'DELETE FROM acl.roles_actions_map WHERE action = \'' . $id . '\'');
    pg_query($connection, 'DELETE FROM acl.actions WHERE id = \'' . $id . '\'');
}

/**
 * @param  resource $connection
 * @param  string   $resource
 * @param  string   $action
 * @param  string   $newAction
 * @return null
 */
function renameAclAction($connection, $resource, $action, $newAction)
{
    $result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'' . $resource . '\' AND name = \'' . $action . '\'');
    if (pg_num_rows($result) == 0) {
        throw new \RuntimeException('The ACL action ' . $resource . '.' . $action . ' does not exist');
    }

    $id = pg_fetch_row($result)[0];

    pg_query($connection, 'UPDATE acl.actions SET name = \'' . $newAction . '\' WHERE id = \'' . $id . '\'');
}
