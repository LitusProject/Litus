<?php

return array(
    'submenus' => array(
        'Cudi' => array(
            'subtitle'    => array('Articles', 'Financial', 'Stock'),
            'items'       => array(
                'cudi_admin_article' => array(
                    'title' => 'Articles',
                    'help'  => 'Here you can find all the articles stored in the database. The general information can be managed here. These articles aren\'t sellable yet. To make them sellable a \'Sale Article\' must be created first.',
                ),

                'cudi_admin_sales_booking' => array(
                    'title' => 'Bookings',
                    'help'  => 'An overview of all bookings made by and for students can be managed in this part.',
                ),
                'cudi_admin_sales_article_discount_template' => array(
                    'title' => 'Discount Templates',
                    'help'  => 'To improve the usability of article discounts, templates are availeble. A template can be assiged to more than one article. Updating the template updates the discount on all these articles.',
                ),
                'cudi_admin_sales_financial' => array(
                    'action' => 'overview',
                    'title'  => 'Financial',
                    'help'   => 'A financial overview of the cudi system can be found here.',
                ),
                'cudi_admin_sales_session_message' => array(
                    'title' => 'Messages',
                    'help'  => 'Manage the messages of the cudi. These message will be shown on the website next to the opening hours.',
                ),
                'cudi_admin_sales_session_openinghour' => array(
                    'title' => 'Opening Hours',
                    'help'  => 'Manage the opening hours of the cudi. These opening hours will be shown on the website.',
                ),

                'cudi_admin_prof_action' => array(
                    'title' => 'Prof Actions',
                    'help'  => 'Manage and confirm the actions executed by a docent in the \'Prof App\'. Docents can manage there courses and add articles to them in the \'Prof App\'.',
                ),

                'cudi_admin_sales_article' => array(
                    'title' => 'Sale Articles',
                    'help'  => 'The articles that can be found here are sellable to students. These are all linked to a regular article found in the \'Articles\' menu. Information about selling like the prices can be managed here.',
                ),
                'cudi_admin_sales_session' => array(
                    'title' => 'Sale Sessions',
                    'help'  => 'To sell articles to students there must be an active \'Sale Session\'. These can be managed in this part.',
                ),

                'cudi_admin_special_action' => array(
                    'title' => 'Special Actions',
                    'help'  => 'Execute some special actions for cudi.',
                ),

                'cudi_admin_stock' => array(
                    'title' => 'Stock',
                    'help'  => 'Manage the stock of cudi, also orders and deliveries can be managed in this part.',
                ),

                'cudi_admin_supplier' => array(
                    'title' => 'Suppliers',
                    'help'  => 'Manage the available suppliers for cudi orders and deliveries.',
                ),

                'cudi_admin_syllabus' => array(
                    'title' => 'Syllabus',
                    'help'  => 'See overview of literature lists and total costs per study.',
                ),

                'cudi_admin_isic' => array(
                    'title' => 'ISIC Cards',
                    'help'  => 'Manage the ISIC Card reservations',
                ),
                'cudi_admin_retail' => array(
                    'title' => 'Retail',
                    'help'  => 'Manage Retail (second-hand) shop for students',
                ),
            ),
            'controllers' => array(
                'cudi_admin_article_subject',
                'cudi_admin_article_comment',
                'cudi_admin_article_file',
                'cudi_admin_sales_article_discount',
                'cudi_admin_sales_article_barcode',
                'cudi_admin_sales_article_restriction',
                'cudi_admin_sales_financial_sold',
                'cudi_admin_sales_financial_delivered',
                'cudi_admin_sales_financial_ordered',
                'cudi_admin_sales_financial_split',
                'cudi_admin_sales_session_restriction',
                'cudi_admin_supplier_user',
                'cudi_admin_syllabus',
                'cudi_admin_stock_period',
                'cudi_admin_stock_order',
                'cudi_admin_stock_delivery',
                'cudi_admin_stock_retour',
                'cudi_admin_isic',
                'cudi_admin_retail',
            ),
        ),
    ),
);
