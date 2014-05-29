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

pg_query($connection, 'INSERT INTO acl.resources VALUES (\'secretary_admin_export\', \'secretarybundle\')');

$result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'secretary_admin_registration\' AND name = \'export\'');
if (0 == pg_num_rows($result))
    throw new \RuntimeException('The ACL action secretary_admin_registration.export does not exist');

$id = pg_fetch_row($result)[0];

pg_query($connection, 'UPDATE acl.actions SET resource = \'secretary_admin_export\' WHERE id = \'' . $id . '\'');

$result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'secretary_admin_registration\' AND name = \'download\'');
if (0 == pg_num_rows($result))
    throw new \RuntimeException('The ACL action secretary_admin_registration.download does not exist');

$id = pg_fetch_row($result)[0];

pg_query($connection, 'UPDATE acl.actions SET resource = \'secretary_admin_export\' WHERE id = \'' . $id . '\'');
