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

// Clear these tables (or columns)
echo ' -> Clear all tables that will be updated' . PHP_EOL;
pg_query($connection, 'DROP TABLE IF EXISTS br.invoices_entries');
pg_query($connection, 'DROP TABLE IF EXISTS br.invoice_history');
pg_query($connection, 'DROP TABLE IF EXISTS br.contract_history');
pg_query($connection, 'DROP TABLE IF EXISTS br.contracts_entries');
pg_query($connection, 'DROP TABLE IF EXISTS br.orders_entries');
pg_query($connection, 'DROP TABLE IF EXISTS br.contracts');
pg_query($connection, 'DROP TABLE IF EXISTS br.invoices');
pg_query($connection, 'DROP TABLE IF EXISTS br.orders');

exec('php bin/doctrine.php orm:schema-tool:update --force', $output, $returnValue);
