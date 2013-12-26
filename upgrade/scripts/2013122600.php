<?php

try {
    removeAclAction($connection, 'cudi_admin_sales_article', 'sellProf');
} catch(Exception $e) {
    echo 'ACL action cudi_admin_sales_article.sellProf was not found' . PHP_EOL;
}

pg_query($connection, 'ALTER TABLE cudi.sales_sale_items ADD inheritance_type VARCHAR(255)');
pg_query($connection, 'UPDATE cudi.sales_sale_items SET inheritance_type = \'regular\'');
