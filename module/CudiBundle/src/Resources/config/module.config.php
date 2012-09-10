<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'router' => array(
        'routes' => array(
            'cudi_install' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/cudi',
                    'defaults' => array(
                        'controller' => 'cudi_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article[/:action[/:id][/page/:page][/:field/:string]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_article_subject'=> array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/subject[/:action[/:id]][/:academicyear]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_article_subject',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_article_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/comment[/:action[/:id[/:article]]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_article_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_article_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/article/file[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_article_file',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_sales_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article[/:action[/:id][/page/:page][/:academicyear][/:field/:string]]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'field'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'       => '[%a-zA-Z0-9_-]*',
                        'page'         => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_sales_article_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/article/:academicyear/typeahead[/:string]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_article',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'admin_sales_discount' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/discount[/:action[/:id]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_discount',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_sales_booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/booking[/:action[/:id][/period/:period][/page/:page][:type[/:field/:string]]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'period'  => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9_-]*',
                        'type'    => '[a-zA-Z][%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_booking',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_sales_session' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/session[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_session',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_sales_financial' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/sales/financial[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_sales_financial',
                        'action'     => 'sales',
                    ),
                ),
            ),
            'admin_supplier' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/supplier[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_supplier',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_supplier_user' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/supplier/user[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_supplier_user',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_stock' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock[/:action[/:id][/:field/:string][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[%a-zA-Z0-9_-]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_stock_period' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/period[/:action[/:id[/:field/:string]][/page/:page]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'  => '[%a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock_period',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_stock_order' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/order[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock_order',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_stock_delivery' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/delivery[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock_delivery',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_stock_delivery_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/article/:academicyear/typeahead[/:string]',
                    'constraints' => array(
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'string'       => '[%a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock_delivery',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'admin_stock_retour' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/stock/retour[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_stock_retour',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_prof_action' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/prof/actions[/:action[/:id][/page/:page]]',
                    'contraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_prof_action',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_cudi_mail' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/cudi/mail',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'admin_cudi_mail',
                        'action'     => 'send',
                    ),
                ),
            ),
            'sale_queue' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/cudi/queue[/:action]/:session',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'sale_queue',
                        'action'     => 'index',
                    ),
                ),
            ),
            'sale_sale' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/cudi/sale[/:action]/:session[/:id]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session' => '[0-9]*',
                        'id'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'sale_sale',
                        'action'     => 'index',
                    ),
                ),
            ),
            'supplier_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'supplier_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'supplier_auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier/auth[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'supplier_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'supplier_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/supplier/article[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'supplier_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof[/:action[/page/:page]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'     => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'prof_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '[/:language]/cudi/prof/auth[/:action[/identification/:identification[/hash/:hash]]]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'prof_subject' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_subject',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_subject_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject/typeahead[/:string]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9_-]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_subject',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'prof_article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_article',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_article_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/typeahead[/:string]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9_-]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_article',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'prof_article_mapping' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/mapping[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_article_mapping',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/files[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_file',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_article_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/article/comments[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_article_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_subject_comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/subject/comments[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_subject_comment',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_prof' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/prof[/:action[/:id]]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_prof',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'prof_typeahead' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/prof/prof/typeahead[/:string]',
                    'constraints' => array(
                        'string'   => '[%a-zA-Z0-9_-]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'prof_prof',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'reservation' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cudi/reservation[/:action]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-zA-Z][a-zA-Z_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'reservation',
                        'action'     => 'index',
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
                'filename' => __DIR__ . '/../translations/reservations.site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/reservations.site.nl.php',
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
                    'CudiBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'cudibundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'cudi_install'          => 'CudiBundle\Controller\Admin\InstallController',

            'admin_article'         => 'CudiBundle\Controller\Admin\ArticleController',
            'admin_article_subject' => 'CudiBundle\Controller\Admin\Article\SubjectMapController',
            'admin_article_comment' => 'CudiBundle\Controller\Admin\Article\CommentController',
            'admin_article_file'    => 'CudiBundle\Controller\Admin\Article\FileController',
            'admin_sales_article'   => 'CudiBundle\Controller\Admin\Sales\ArticleController',
            'admin_sales_discount'  => 'CudiBundle\Controller\Admin\Sales\DiscountController',
            'admin_sales_booking'   => 'CudiBundle\Controller\Admin\Sales\BookingController',
            'admin_sales_session'   => 'CudiBundle\Controller\Admin\Sales\SessionController',
            'admin_sales_financial' => 'CudiBundle\Controller\Admin\Sales\FinancialController',
            'admin_supplier'        => 'CudiBundle\Controller\Admin\SupplierController',
            'admin_supplier_user'   => 'CudiBundle\Controller\Admin\Supplier\UserController',
            'admin_stock'           => 'CudiBundle\Controller\Admin\StockController',
            'admin_stock_period'    => 'CudiBundle\Controller\Admin\Stock\PeriodController',
            'admin_stock_delivery'  => 'CudiBundle\Controller\Admin\Stock\DeliveryController',
            'admin_stock_retour'    => 'CudiBundle\Controller\Admin\Stock\RetourController',
            'admin_stock_order'     => 'CudiBundle\Controller\Admin\Stock\OrderController',
            'admin_prof_action'     => 'CudiBundle\Controller\Admin\Prof\ActionController',
            'admin_cudi_mail'       => 'CudiBundle\Controller\Admin\MailController',

            'sale_sale'             => 'CudiBundle\Controller\Sale\SaleController',
            'sale_queue'            => 'CudiBundle\Controller\Sale\QueueController',

            'supplier_index'        => 'CudiBundle\Controller\Supplier\IndexController',
            'supplier_article'      => 'CudiBundle\Controller\Supplier\ArticleController',
            'supplier_auth'         => 'CudiBundle\Controller\Supplier\AuthController',

            'prof_index'            => 'CudiBundle\Controller\Prof\IndexController',
            'prof_auth'             => 'CudiBundle\Controller\Prof\AuthController',
            'prof_article'          => 'CudiBundle\Controller\Prof\ArticleController',
            'prof_article_mapping'  => 'CudiBundle\Controller\Prof\Article\MappingController',
            'prof_file'             => 'CudiBundle\Controller\Prof\Article\FileController',
            'prof_article_comment'  => 'CudiBundle\Controller\Prof\Article\CommentController',
            'prof_prof'             => 'CudiBundle\Controller\Prof\ProfController',
            'prof_subject'          => 'CudiBundle\Controller\Prof\SubjectController',
            'prof_subject_comment'  => 'CudiBundle\Controller\Prof\Subject\CommentController',
            
            'reservation'           => 'CudiBundle\Controller\Reservation\ReservationController',
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
                ),
            ),
        ),
    ),
);
