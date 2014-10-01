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

return array(
    'routes' => array(
        'cudi_admin_article' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/article[/:action[/:id][/page/:page][/:field/:string]][/]',
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
        'cudi_admin_article_subject' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/article/subject[/:action[/:id]][/:academicyear][/]',
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
                'route' => '/admin/cudi/article/comment[/:action[/:id[/:article]][/page/:page]][/]',
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
                'route' => '/admin/cudi/article/file[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/sales/article[/:action[/:id][/:academicyear][/semester/:semester][/page/:page][/:field/:string]][/]',
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
                'route' => '/admin/cudi/sales/article/:academicyear/typeahead[/:string][/]',
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
        'cudi_admin_sales_article_sale' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/article/sale[/:action[/:id]][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'cudi_admin_sales_article_sale',
                    'action'     => 'sale',
                ),
            ),
        ),
        'cudi_admin_sales_article_discount' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/article/discount[/:action[/:id]][/]',
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
        'cudi_admin_sales_article_discount_template' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/article/discount/template[/:action[/:id]][/]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'cudi_admin_sales_article_discount_template',
                    'action'     => 'manage',
                ),
            ),
        ),
        'cudi_admin_sales_article_barcode' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/article/barcode[/:action[/:id]][/]',
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
                'route' => '/admin/cudi/sales/article/restriction[/:action[/:id]][/]',
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
                'route' => '/admin/cudi/sales/booking[/:action[/:id][/period/:period][/:type[/:field/:string]][/page/:page][/date/:date][/number/:number]][/]',
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
                'route' => '/admin/cudi/sales/session[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/sales/session/restriction[/:action[/:id]][/]',
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
                'route' => '/admin/cudi/sales/session/openinghours[/:action[/:id]][/]',
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
                'route' => '/admin/cudi/sales/financial[/:action[/:id][/:academicyear]][/]',
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
                'route' => '/admin/cudi/sales/financial/sold[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
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
        'cudi_admin_sales_financial_returned' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/financial/returned[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                ),
                'defaults' => array(
                    'controller' => 'cudi_admin_sales_financial_returned',
                    'action'     => 'individual',
                ),
            ),
        ),
        'cudi_admin_sales_financial_ordered' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/sales/financial/ordered[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
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
                'route' => '/admin/cudi/sales/financial/delivered[/:action[/:id][/:academicyear][/page/:page][/:field/:string]][/]',
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
                'route' => '/admin/cudi/supplier[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/supplier/user[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/stock[/:action[/:id][/semester/:semester][/page/:page][/:field/:string]][/]',
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
                'route' => '/admin/cudi/stock/period[/:action[/:id[/:field/:string]][/page/:page]][/]',
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
                'route' => '/admin/cudi/stock/order[/:action[/:id[/:date][/:order]][/page/:page][/:field/:string]][/]',
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
                'route' => '/admin/cudi/stock/delivery[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/stock/article/:academicyear/typeahead[/:string][/]',
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
                'route' => '/admin/cudi/stock/retour[/:action[/:id][/page/:page]][/]',
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
                'route' => '/admin/cudi/prof/actions[/:action[/:id][/page/:page]][/]',
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
        'cudi_admin_special_action' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/cudi/special[/:action[/:id]][/]',
                'contraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'cudi_admin_special_action',
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
                    'action'     => 'signin',
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
        'cudi_sale_auth' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/cudi/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                'constraints' => array(
                    'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'identification' => '[mrsu][0-9]{7}',
                    'hash'           => '[a-zA-Z0-9_-]*',
                    'language'       => '[a-z]{2}',
                ),
                'defaults' => array(
                    'controller' => 'cudi_sale_auth',
                    'action'     => 'login',
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

    'controllers' => array(
        'cudi_admin_article'                            => 'CudiBundle\Controller\Admin\ArticleController',
        'cudi_admin_article_subject'                    => 'CudiBundle\Controller\Admin\Article\SubjectMapController',
        'cudi_admin_article_comment'                    => 'CudiBundle\Controller\Admin\Article\CommentController',
        'cudi_admin_article_file'                       => 'CudiBundle\Controller\Admin\Article\FileController',
        'cudi_admin_sales_article'                      => 'CudiBundle\Controller\Admin\Sale\ArticleController',
        'cudi_admin_sales_article_sale'                 => 'CudiBundle\Controller\Admin\Sale\Article\SaleController',
        'cudi_admin_sales_article_barcode'              => 'CudiBundle\Controller\Admin\Sale\Article\BarcodeController',
        'cudi_admin_sales_article_discount'             => 'CudiBundle\Controller\Admin\Sale\Article\DiscountController',
        'cudi_admin_sales_article_discount_template'    => 'CudiBundle\Controller\Admin\Sale\Article\Discount\TemplateController',
        'cudi_admin_sales_article_restriction'          => 'CudiBundle\Controller\Admin\Sale\Article\RestrictionController',
        'cudi_admin_sales_booking'                      => 'CudiBundle\Controller\Admin\Sale\BookingController',
        'cudi_admin_sales_session'                      => 'CudiBundle\Controller\Admin\Sale\SessionController',
        'cudi_admin_sales_session_restriction'          => 'CudiBundle\Controller\Admin\Sale\Session\RestrictionController',
        'cudi_admin_sales_session_openinghour'          => 'CudiBundle\Controller\Admin\Sale\Session\OpeningHourController',
        'cudi_admin_sales_financial'                    => 'CudiBundle\Controller\Admin\Sale\FinancialController',
        'cudi_admin_sales_financial_sold'               => 'CudiBundle\Controller\Admin\Sale\Financial\SoldController',
        'cudi_admin_sales_financial_returned'           => 'CudiBundle\Controller\Admin\Sale\Financial\ReturnedController',
        'cudi_admin_sales_financial_delivered'          => 'CudiBundle\Controller\Admin\Sale\Financial\DeliveredController',
        'cudi_admin_sales_financial_ordered'            => 'CudiBundle\Controller\Admin\Sale\Financial\OrderedController',
        'cudi_admin_supplier'                           => 'CudiBundle\Controller\Admin\SupplierController',
        'cudi_admin_supplier_user'                      => 'CudiBundle\Controller\Admin\Supplier\UserController',
        'cudi_admin_stock'                              => 'CudiBundle\Controller\Admin\StockController',
        'cudi_admin_stock_period'                       => 'CudiBundle\Controller\Admin\Stock\PeriodController',
        'cudi_admin_stock_delivery'                     => 'CudiBundle\Controller\Admin\Stock\DeliveryController',
        'cudi_admin_stock_retour'                       => 'CudiBundle\Controller\Admin\Stock\RetourController',
        'cudi_admin_stock_order'                        => 'CudiBundle\Controller\Admin\Stock\OrderController',
        'cudi_admin_prof_action'                        => 'CudiBundle\Controller\Admin\Prof\ActionController',
        'cudi_admin_special_action'                     => 'CudiBundle\Controller\Admin\SpecialActionController',
        'cudi_admin_mail'                               => 'CudiBundle\Controller\Admin\MailController',

        'cudi_sale_sale'                                => 'CudiBundle\Controller\Sale\SaleController',
        'cudi_sale_queue'                               => 'CudiBundle\Controller\Sale\QueueController',
        'cudi_sale_auth'                                => 'CudiBundle\Controller\Sale\AuthController',

        'cudi_supplier_index'                           => 'CudiBundle\Controller\Supplier\IndexController',
        'cudi_supplier_article'                         => 'CudiBundle\Controller\Supplier\ArticleController',
        'cudi_supplier_auth'                            => 'CudiBundle\Controller\Supplier\AuthController',

        'cudi_prof_index'                               => 'CudiBundle\Controller\Prof\IndexController',
        'cudi_prof_auth'                                => 'CudiBundle\Controller\Prof\AuthController',
        'cudi_prof_article'                             => 'CudiBundle\Controller\Prof\ArticleController',
        'cudi_prof_article_mapping'                     => 'CudiBundle\Controller\Prof\Article\MappingController',
        'cudi_prof_file'                                => 'CudiBundle\Controller\Prof\Article\FileController',
        'cudi_prof_article_comment'                     => 'CudiBundle\Controller\Prof\Article\CommentController',
        'cudi_prof_prof'                                => 'CudiBundle\Controller\Prof\ProfController',
        'cudi_prof_subject'                             => 'CudiBundle\Controller\Prof\SubjectController',
        'cudi_prof_subject_comment'                     => 'CudiBundle\Controller\Prof\Subject\CommentController',
        'cudi_prof_help'                                => 'CudiBundle\Controller\Prof\HelpController',

        'cudi_booking'                                  => 'CudiBundle\Controller\BookingController',
        'cudi_opening_hour'                             => 'CudiBundle\Controller\OpeningHourController',
    ),
);
