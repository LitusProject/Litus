<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'router' => array(
        'routes' => array(
            'cudi_install' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/cudi[/]',
                    'defaults' => array(
                        'controller' => 'cudi_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cudi_admin_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article[/:action[/:id][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9:.,_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_article_subject'=> array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/subject[/:action[/:id]][/:academicyear][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_article_subject',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_article_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/comment[/:action[/:id[/:article]][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_article_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_article_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/file[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_article_file',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article[/:action[/:id][/:academicyear][/semester/:semester][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                        'page'         => '[0-9]*',
                        'semester'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_article_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article/:academicyear/typeahead[/:string][/]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_article',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'cudi_admin_sales_article_discount' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article/discount[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_article_discount',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_article_barcode' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article/barcode[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_article_barcode',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_article_restriction' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article/restriction[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_article_restriction',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/booking[/:action[/:id][/period/:period][/:type[/:field/:string]][/page/:page][/date/:date][/number/:number]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'period'  => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9:.,_-]*',
                        'type'    => '[a-zA-Z][%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                        'date'    => '[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_booking',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_session' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/session[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_session',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_session_restriction' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/session/restriction[/:action[/:id]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_session_restriction',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_session_openinghour' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/session/openinghours[/:action[/:id]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_session_openinghour',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_sales_financial' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/financial[/:action[/:id][/:academicyear]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_financial',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'cudi_admin_sales_financial_sold' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/financial/sold[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[%a-zA-Z0-9:.,_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_financial_sold',
                        'action'     => 'individual',
                    ),
                ),
            ),
            'cudi_admin_sales_financial_ordered' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/financial/ordered[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[%a-zA-Z0-9:.,_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_financial_ordered',
                        'action'     => 'individual',
                    ),
                ),
            ),
            'cudi_admin_sales_financial_delivered' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/financial/delivered[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[%a-zA-Z0-9:.,_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_sales_financial_delivered',
                        'action'     => 'individual',
                    ),
                ),
            ),
            'cudi_admin_supplier' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/supplier[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_supplier',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_supplier_user' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/supplier/user[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_supplier_user',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_stock' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock[/:action[/:id][/semester/:semester][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                        'page'         => '[0-9]*',
                        'semester'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_stock_period' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/period[/:action[/:id[/:field/:string]][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9:.,_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock_period',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_stock_order' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/order[/:action[/:id[/:date][/:order]][/page/:page][/:field/:string]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9:.,_-]*',
                        'page'    => '[0-9]*',
                        'date'    => '[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}',
                        'order'   => '[a-zA-Z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock_order',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_stock_delivery' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/delivery[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock_delivery',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_stock_delivery_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/article/:academicyear/typeahead[/:string][/]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock_delivery',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'cudi_admin_stock_retour' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/retour[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_stock_retour',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_prof_action' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/prof/actions[/:action[/:id][/page/:page]][/]',
                    'contraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_prof_action',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_admin_mail' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/cudi/mail[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_admin_mail',
                        'action'     => 'send',
                    ),
                ),
            ),
            'cudi_sale_queue' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/cudi/queue[[/:action]/:session][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_sale_queue',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cudi_sale_sale' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/cudi/sale[/:action[/:session[/:id]]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session' => '[0-9]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_sale_sale',
                        'action'     => 'sale',
                    ),
                ),
            ),
            'cudi_supplier_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_supplier_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cudi_supplier_auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier/auth[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_supplier_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'cudi_supplier_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier/article[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_supplier_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof[/:action[/page/:page]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'     => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cudi_prof_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                        'language'       => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'cudi_prof_subject' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_subject',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_subject_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_subject',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'cudi_prof_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_article_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_article',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'cudi_prof_article_mapping' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/mapping[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_article_mapping',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/files[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_file',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_article_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/comments[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_article_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_subject_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject/comments[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_subject_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_prof' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/prof[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_prof',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'cudi_prof_help' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/help[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_help',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cudi_prof_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/prof/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_prof_prof',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'cudi_booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/booking[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                        'id'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_booking',
                        'action'     => 'view',
                    ),
                ),
            ),
            'cudi_opening_hour' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/opening_hours[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                        'id'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'cudi_opening_hour',
                        'action'     => 'week',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/supplier.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/supplier.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/prof.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/prof.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'cudi_layout' => __DIR__ . '/../layouts',
            'cudi_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'CudiBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'cudibundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'cudi_install'                         => 'CudiBundle\Controller\Admin\InstallController',

            'cudi_admin_article'                   => 'CudiBundle\Controller\Admin\ArticleController',
            'cudi_admin_article_subject'           => 'CudiBundle\Controller\Admin\Article\SubjectMapController',
            'cudi_admin_article_comment'           => 'CudiBundle\Controller\Admin\Article\CommentController',
            'cudi_admin_article_file'              => 'CudiBundle\Controller\Admin\Article\FileController',
            'cudi_admin_sales_article'             => 'CudiBundle\Controller\Admin\Sale\ArticleController',
            'cudi_admin_sales_article_barcode'     => 'CudiBundle\Controller\Admin\Sale\Article\BarcodeController',
            'cudi_admin_sales_article_discount'    => 'CudiBundle\Controller\Admin\Sale\Article\DiscountController',
            'cudi_admin_sales_article_restriction' => 'CudiBundle\Controller\Admin\Sale\Article\RestrictionController',
            'cudi_admin_sales_booking'             => 'CudiBundle\Controller\Admin\Sale\BookingController',
            'cudi_admin_sales_session'             => 'CudiBundle\Controller\Admin\Sale\SessionController',
            'cudi_admin_sales_session_restriction' => 'CudiBundle\Controller\Admin\Sale\Session\RestrictionController',
            'cudi_admin_sales_session_openinghour' => 'CudiBundle\Controller\Admin\Sale\Session\OpeningHourController',
            'cudi_admin_sales_financial'           => 'CudiBundle\Controller\Admin\Sale\FinancialController',
            'cudi_admin_sales_financial_sold'      => 'CudiBundle\Controller\Admin\Sale\Financial\SoldController',
            'cudi_admin_sales_financial_delivered' => 'CudiBundle\Controller\Admin\Sale\Financial\DeliveredController',
            'cudi_admin_sales_financial_ordered'   => 'CudiBundle\Controller\Admin\Sale\Financial\OrderedController',
            'cudi_admin_supplier'                  => 'CudiBundle\Controller\Admin\SupplierController',
            'cudi_admin_supplier_user'             => 'CudiBundle\Controller\Admin\Supplier\UserController',
            'cudi_admin_stock'                     => 'CudiBundle\Controller\Admin\StockController',
            'cudi_admin_stock_period'              => 'CudiBundle\Controller\Admin\Stock\PeriodController',
            'cudi_admin_stock_delivery'            => 'CudiBundle\Controller\Admin\Stock\DeliveryController',
            'cudi_admin_stock_retour'              => 'CudiBundle\Controller\Admin\Stock\RetourController',
            'cudi_admin_stock_order'               => 'CudiBundle\Controller\Admin\Stock\OrderController',
            'cudi_admin_prof_action'               => 'CudiBundle\Controller\Admin\Prof\ActionController',
            'cudi_admin_mail'                      => 'CudiBundle\Controller\Admin\MailController',

            'cudi_sale_sale'                       => 'CudiBundle\Controller\Sale\SaleController',
            'cudi_sale_queue'                      => 'CudiBundle\Controller\Sale\QueueController',

            'cudi_supplier_index'                  => 'CudiBundle\Controller\Supplier\IndexController',
            'cudi_supplier_article'                => 'CudiBundle\Controller\Supplier\ArticleController',
            'cudi_supplier_auth'                   => 'CudiBundle\Controller\Supplier\AuthController',

            'cudi_prof_index'                      => 'CudiBundle\Controller\Prof\IndexController',
            'cudi_prof_auth'                       => 'CudiBundle\Controller\Prof\AuthController',
            'cudi_prof_article'                    => 'CudiBundle\Controller\Prof\ArticleController',
            'cudi_prof_article_mapping'            => 'CudiBundle\Controller\Prof\Article\MappingController',
            'cudi_prof_file'                       => 'CudiBundle\Controller\Prof\Article\FileController',
            'cudi_prof_article_comment'            => 'CudiBundle\Controller\Prof\Article\CommentController',
            'cudi_prof_prof'                       => 'CudiBundle\Controller\Prof\ProfController',
            'cudi_prof_subject'                    => 'CudiBundle\Controller\Prof\SubjectController',
            'cudi_prof_subject_comment'            => 'CudiBundle\Controller\Prof\Subject\CommentController',
            'cudi_prof_help'                       => 'CudiBundle\Controller\Prof\HelpController',

            'cudi_booking'                         => 'CudiBundle\Controller\BookingController',
            'cudi_opening_hour'                    => 'CudiBundle\Controller\OpeningHourController',
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'cudibundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'sale_css' => array(
                        'assets' => array(
                            'sale/less/base.less',
                        ),
                        'filters' => array(
                            'sale_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'sale_css.css',
                        ),
                    ),
                    'supplier_css' => array(
                        'assets' => array(
                            'supplier/less/base.less',
                        ),
                        'filters' => array(
                            'supplier_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'supplier_css.css',
                        ),
                    ),
                    'supplier_nav' => array(
                        'assets' => array(
                            'admin/js/supplierNavigation.js',
                        ),
                    ),
                    'queue_js' => array(
                        'assets' => array(
                            'queue/js/*.js',
                        ),
                    ),
                    'sale_js' => array(
                        'assets' => array(
                            'sale/js/*.js',
                        ),
                    ),
                    'prof_css' => array(
                        'assets' => array(
                            'prof/less/base.less',
                        ),
                        'filters' => array(
                            'prof_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'prof_css.css',
                        ),
                    ),
                    'booking_css' => array(
                        'assets' => array(
                            'booking/less/base.less',
                        ),
                        'filters' => array(
                            'booking_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'booking_css.css',
                        ),
                    ),
                    'opening_hour_css' => array(
                        'assets' => array(
                            'opening-hour/less/schedule.less',
                        ),
                        'filters' => array(
                            'opening_hour_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'opening_hour_css.css',
                        ),
                    ),
                    'opening_hour_js' => array(
                        'assets' => array(
                            'opening-hour/js/*.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
