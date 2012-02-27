<?php
return array(
	'display_exceptions'	=> true,
	'di'					=> array(
		'instance' => array(
			'alias' => array(
				'admin_article'	      => 'CudiBundle\Controller\Admin\ArticleController',
				'admin_booking'	      => 'CudiBundle\Controller\Admin\BookingController',
				'admin_delivery'      => 'CudiBundle\Controller\Admin\DeliveryController',
				'admin_order'	      => 'CudiBundle\Controller\Admin\OrderController',
				'admin_sale'          => 'CudiBundle\Controller\Admin\SaleController',
				'admin_stock'	      => 'CudiBundle\Controller\Admin\StockController',
				'sale'	              => 'CudiBundle\Controller\SaleController',
				'queue'	              => 'CudiBundle\Controller\QueueController',
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
                                    		'sale/less/sale.less',
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
				'route' => '/admin/article/search/[/:field[/:string]]',
				'constraints' => array(
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
					'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
					'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
		'sale' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/sale[/:controller[/:action]]',
				'constraints' => array(
					'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'sale',
					'action'     => 'index',
				),
			),
		)
	),
);