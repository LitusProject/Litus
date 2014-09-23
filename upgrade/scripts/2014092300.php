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

pg_query($connection, 'CREATE TABLE users.barcodes_ean12 (id BIGINT NOT NULL, barcode BIGINT NOT NULL, PRIMARY KEY(id))');
pg_query($connection, 'CREATE TABLE users.barcodes_qr (id BIGINT NOT NULL, barcode VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
pg_query($connection, 'ALTER TABLE users.barcodes ADD inheritance_type VARCHAR(255)');
pg_query($connection, 'UPDATE users.barcodes SET inheritance_type = \'ean12\'');

$result = pg_query($connection, 'SELECT id, barcode FROM users.barcodes');
while ($row = pg_fetch_assoc($result))
    pg_query('INSERT INTO users.barcodes_ean12 (id, barcode) VALUES (\'' . $row['id'] . '\', \'' . $row['barcode'] . '\')');
