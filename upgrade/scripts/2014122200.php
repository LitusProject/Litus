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

$result = pg_query($connection, "SELECT p.id,p.first_name, p.last_name,'is geen student'
FROM users.people AS p
WHERE NOT EXISTS
(
SELECT *
FROM users.university_statuses AS u
WHERE p.id = u.person AND u.academic_year = 12
) AND p.username LIKE 'r0%' AND p.username >= 'r0240000'
ORDER BY p.id");

while ($row = pg_fetch_row($result)) {
    $lastID = pg_fetch_row(pg_query($connection, "SELECT u.id FROM users.university_statuses AS u ORDER BY u.id DESC"));
    $id = ((int) $lastID[0])+1;
    pg_query($connection, "INSERT INTO users.university_statuses VALUES (" . $id . "," . $row[0] . ", '12', 'student' )");
}
