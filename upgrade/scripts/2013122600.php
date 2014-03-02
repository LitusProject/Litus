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

try {
    removeAclAction($connection, 'cudi_admin_sales_article', 'sellProf');
} catch (Exception $e) {
    echo 'ACL action cudi_admin_sales_article.sellProf was not found' . PHP_EOL;
}

pg_query($connection, 'ALTER TABLE cudi.sales_sale_items ADD inheritance_type VARCHAR(255)');
pg_query($connection, 'UPDATE cudi.sales_sale_items SET inheritance_type = \'regular\'');
