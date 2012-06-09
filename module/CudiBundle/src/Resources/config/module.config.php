<?php
return array(
	'di'					=> array(
		'instance' => array(
			'alias' => array(
				'cudi_install'		     => 'CudiBundle\Controller\Admin\InstallController',
				
				'admin_article'	         => 'CudiBundle\Controller\Admin\ArticleController',
				'admin_article_subject'  => 'CudiBundle\Controller\Admin\Article\SubjectMapController',
				'admin_article_comment'  => 'CudiBundle\Controller\Admin\Article\CommentController',
				'admin_article_file'	 => 'CudiBundle\Controller\Admin\Article\FileController',
				'admin_sales_article'    => 'CudiBundle\Controller\Admin\Sales\ArticleController',
				'admin_sales_discount'   => 'CudiBundle\Controller\Admin\Sales\DiscountController',
				'admin_sales_booking'	 => 'CudiBundle\Controller\Admin\Sales\BookingController',
				'admin_sales_session'    => 'CudiBundle\Controller\Admin\Sales\SessionController',
				'admin_supplier'	     => 'CudiBundle\Controller\Admin\Supplier\SupplierController',
				'admin_supplier_user'    => 'CudiBundle\Controller\Admin\Supplier\UserController',
				'admin_stock'	         => 'CudiBundle\Controller\Admin\Stock\StockController',
				'admin_stock_period'	 => 'CudiBundle\Controller\Admin\Stock\PeriodController',
				'admin_stock_delivery'   => 'CudiBundle\Controller\Admin\Stock\DeliveryController',
				'admin_stock_retour'     => 'CudiBundle\Controller\Admin\Stock\RetourController',
				'admin_stock_order'	     => 'CudiBundle\Controller\Admin\Stock\OrderController',
				/*'admin_financial'        => 'CudiBundle\Controller\Admin\FinancialController',
				
				'sale_sale'	             => 'CudiBundle\Controller\Sale\SaleController',
				'sale_queue'	         => 'CudiBundle\Controller\Sale\QueueController',*/
				
				'supplier_index'         => 'CudiBundle\Controller\Supplier\IndexController',
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
                                    'supplier_nav' => array(
                                        'assets'  => array(
                                            'admin/js/supplierNavigation.js',
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
				'route' => '/admin/article[/:action[/:id][/:field/:string]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
					'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string'  => '[%a-zA-Z0-9_-]*',
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
		'admin_article_subject'=> array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
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
			'type'    => 'Zend\Mvc\Router\Http\Segment',
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
			'type'    => 'Zend\Mvc\Router\Http\Segment',
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
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sales/article[/:action[/:id][/:academicyear][/:field/:string]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
    				'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
    				'string' => '[%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_sales_article',
					'action'     => 'manage',
				),
			),
		),
		'admin_sales_article_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sales/article/manage[/:page][/:academicyear]',
				'constraints' => array(
					'page' => '[0-9]*',
        			'academicyear' => '[0-9]{4}-[0-9]{4}',
		        ),
				'defaults' => array(
					'controller' => 'admin_sales_article',
					'action'     => 'manage',
				), 
			),
		),
		'admin_sales_discount' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
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
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sales/booking[/:action[/:id][/period/:period][:type[/:field/:string]]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
					'period'  => '[0-9]*',
					'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string'  => '[a-zA-Z][%a-zA-Z0-9_-]*',
					'type'    => '[a-zA-Z][%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_sales_booking',
					'action'     => 'manage',
				),
			),
		),
		'admin_sales_booking_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sales/booking/manage[/:page]',
				'constraints' => array(
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_sales_booking',
					'action'     => 'manage',
				),
			),
		),
		'admin_sales_session' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/sales/session[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_sales_session',
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
		'admin_supplier_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/supplier/manage[/:page]',
				'constraints' => array(
				    'page' => '[0-9]*',
		        ),
				'defaults' => array(
					'controller' => 'admin_supplier',
					'action'     => 'manage',
				), 
			),
		),
		'admin_supplier_user' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/supplier/user[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_supplier_user',
					'action'     => 'manage',
				),
			),
		),
		'admin_supplier_user_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/supplier/user/manage/page[/:page]',
				'constraints' => array(
				    'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_supplier_user',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock[/:action[/:id][/:field/:string]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
					'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/manage[/:page]',
				'constraints' => array(
					'page'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_period' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/period[/:action[/:id[/:field/:string]]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
					'field'   => '[a-zA-Z][a-zA-Z0-9_-]*',
					'string'  => '[%a-zA-Z0-9_-]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_period',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_period_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/period/manage[/:page]',
				'constraints' => array(
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_period',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_period_view_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/period/view/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_period',
					'action'     => 'view',
				),
			),
		),
		'admin_stock_order' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/order[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_order',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_order_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/order/supplier/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_order',
					'action'     => 'supplier',
				),
			),
		),
		'admin_stock_delivery' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/delivery[/:action[/:id]]',
				'constraints' => array(
					'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'      => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_delivery',
					'action'     => 'manage',
				),
			),
		),
		'admin_stock_delivery_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/delivery/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_delivery',
					'action'     => 'supplier',
				),
			),
		),
		'admin_stock_retour' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/retour[/:action[/:id]]',
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
		'admin_stock_retour_paginator' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/stock/retour/:id[/:page]',
				'constraints' => array(
					'id'   => '[0-9]*',
					'page' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_stock_retour',
					'action'     => 'supplier',
				),
			),
		),
		/*'admin_financial' => array(
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
		'admin_financial_stock' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/financial[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_financial',
					'action'     => 'stock',
				),
			),
		),
		'admin_financial_supplier' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/admin/financial[/:action[/:id]]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'id'     => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'admin_financial',
					'action'     => 'supplier',
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
		),*/
		'supplier_index' => array(
			'type'    => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/cudi/supplier[/:action]',
				'constraints' => array(
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					'session' => '[0-9]*',
				),
				'defaults' => array(
					'controller' => 'supplier_index',
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
