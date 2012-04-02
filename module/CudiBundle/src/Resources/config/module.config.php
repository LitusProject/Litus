<?php
return array(
	'di'					=> array(
		'instance' => array(
			'alias' => array(
				'cudi_install'		     => 'CudiBundle\Controller\Admin\InstallerController',
				'admin_article'	         => 'CudiBundle\Controller\Admin\ArticleController',
				'admin_article_subject'  => 'CudiBundle\Controller\Admin\ArticleSubjectMapController',
				'admin_comment'	         => 'CudiBundle\Controller\Admin\CommentController',
				'admin_file'	         => 'CudiBundle\Controller\Admin\FileController',
				'admin_booking'	         => 'CudiBundle\Controller\Admin\BookingController',
				'admin_delivery'         => 'CudiBundle\Controller\Admin\DeliveryController',
				'admin_order'	         => 'CudiBundle\Controller\Admin\OrderController',
				'admin_sale'             => 'CudiBundle\Controller\Admin\SaleController',
				'admin_financial'        => 'CudiBundle\Controller\Admin\FinancialController',
				'admin_stock'	         => 'CudiBundle\Controller\Admin\StockController',
				'admin_supplier'	     => 'CudiBundle\Controller\Admin\SupplierController',
				'sale_sale'	             => 'CudiBundle\Controller\Sale\SaleController',
				'sale_queue'	         => 'CudiBundle\Controller\Sale\QueueController',
				'prof'      	         => 'CudiBundle\Controller\Prof\IndexController',
				'prof_article'           => 'CudiBundle\Controller\Prof\ArticleController',
				'prof_file'              => 'CudiBundle\Controller\Prof\FileController',
				'supplier'               => 'CudiBundle\Controller\Supplier\IndexController',
				'supplier_article'       => 'CudiBundle\Controller\Supplier\ArticleController',
				'supplier_auth'          => 'CudiBundle\Controller\Supplier\AuthController',
            ),
            'assetic_configuration'          => array(
                'parameters' => array(
                    'config' => array(
                        'modules'      => array(
                            'cudibundle' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'sale_css' => array(
                                    	'assets' => array(
                                    		'sale/less/base.less',
                                    	),
                                    	'filters' => array(
                                    		'sale_less' => array(
                                    			'name' => 'LessFilter',
                                    			'parameters' => array(
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
                                    'prof_css' => array(
                                    	'assets' => array(
                                    		'prof/less/base.less',
                                    	),
                                    	'filters' => array(
                                    		'prof_less' => array(
                                    			'name' => 'LessFilter',
                                    			'parameters' => array(
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
                                    'supplier_css' => array(
                                    	'assets' => array(
                                    		'supplier/less/base.less',
                                    	),
                                    	'filters' => array(
                                    		'supplier_less' => array(
                                    			'name' => 'LessFilter',
                                    			'parameters' => array(
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
                                    'queue_js' => array(
                                        'assets'  => array(
                                            'queue/js/*.js',
                                        ),
                                    ),
                                    'sale_js' => array(
                                        'assets'  => array(
                                            'sale/js/*.js',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
			'doctrine_config' => array(
				'parameters' => array(
					'entityPaths' => array(
						'cudibundle' => __DIR__ . '/../../Entity',
					),
				),
			), 
		),
	),
	'routes' => array(
		'cudi_install' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/install/cudi',
				'defaults' => array(
					'controller' => 'cudi_install',
					'action'     => 'index',
				),
			),
		),
		'admin_article' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/article[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_article',
					'action'     => 'manage',
				),
			),
		),
		'admin_article_subject'=> array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/article/subject[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_article_subject',
					'action'     => 'manage',
				),
			),
		),
		'admin_comment' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/comment[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_comment',
					'action'     => 'manage',
				),
			),
		),
		'admin_file' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/file[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_file',
					'action'     => 'manage',
				),
			),
		),
		'admin_article_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/article/manage[/:page]',
				'constraints' => array(
					'page' => '[0-9]*',
                ),
				'defaults' => array(
					'controller' => 'admin_article',
					'action'     => 'manage',
				), 
			),
		),
		'admin_article_search' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/article/search[/:field[/:string]]',
				'constraints' => array(
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
                ),
				'defaults' => array(
					'controller' => 'admin_article',
					'action'     => 'search',
				),
			),
		),
		'admin_booking' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/booking[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_booking',
					'action'     => 'manage',
				),
			),
		),
		'admin_article_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/booking/manage[/:page]',
				'constraints' => array(
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_booking',
					'action'     => 'manage',
				),
			),
		),
		'admin_booking_search' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
			'route' => '/admin/booking/search[/:field/:string]',
				'constraints' => array(
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_booking',
					'action'     => 'search',
				),
			),
		),
		'admin_stock' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_search' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/search[/:field/:string]',
				'constraints' => array(
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock',
					'action'     => 'search',
				),
			),
		),
		'admin_order' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/order[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_order',
					'action'     => 'manage',
				),
			),
		),
		'admin_order_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/order/supplier/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_order',
					'action'     => 'supplier',
				),
			),
		),
		'admin_delivery' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/delivery[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_delivery',
					'action'     => 'manage',
				),
			),
		),
		'admin_delivery_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/delivery/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_delivery',
					'action'     => 'supplier',
				),
			),
		),
		'admin_financial' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/financial[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_financial',
					'action'     => 'sales',
				),
			),
		),
		'admin_sale' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sale[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_sale',
					'action'     => 'manage',
				),
			),
		),
		'admin_supplier' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/supplier[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_supplier',
					'action'     => 'manage',
				),
			),
		),
		'admin_supplier_search' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/supplier/search[/:field/:string]',
				'constraints' => array(
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_supplier',
					'action'     => 'search',
				),
			),
		),
		'sale_queue' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/queue[/:action]/:session',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'sale_queue',
					'action'     => 'index',
				),
			),
		),
		'sale_sale' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/sale[/:action]/:session',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'sale_sale',
					'action'     => 'index',
				),
			),
		),
		'prof' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/prof[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'prof',
					'action'     => 'index',
				),
			),
		),
		'prof_article' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/prof/article[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'prof_article',
					'action'     => 'manage',
				),
			),
		),
		'prof_file' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/prof/files[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'prof_file',
					'action'     => 'manage',
				),
			),
		),
		'supplier' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/supplier[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'supplier',
					'action'     => 'index',
				),
			),
		),
		'supplier_auth' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/supplier/auth[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'supplier_auth',
					'action'     => 'login',
				),
			),
		),
		'supplier_article' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/supplier/article[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'supplier_article',
					'action'     => 'manage',
				),
			),
		),
	),
);
