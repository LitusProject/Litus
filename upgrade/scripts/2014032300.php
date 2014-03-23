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

$result = pg_query($connection, 'SELECT id FROM acl.actions WHERE resource = \'cudi_admin_sales_booking\' AND name = \'unassign\'');
if (0 == pg_num_rows($result))
    throw new \RuntimeException('The ACL action cudi_admin_sales_booking.unassign does not exist');
$id = pg_fetch_row($result)[0];

$result = pg_query($connection, 'SELECT * FROM acl.roles_actions_map WHERE action = \'' . $id . '\'');

$roles = array();
while ($row = pg_fetch_row($result)) {
    $roles[] = $row[0];
}

$result = pg_query($connection, 'SELECT nextval(\'acl.actions_id_seq\')');
$searchId = pg_fetch_row($result)[0];
pg_query($connection, 'INSERT INTO acl.actions VALUES(' . $searchId . ', \'cudi_admin_sales_booking\', \'return\')');

foreach ($roles as $role) {
    pg_query($connection, 'INSERT INTO acl.roles_actions_map VALUES(\'' . $role . '\', ' . $searchId . ')');
}
