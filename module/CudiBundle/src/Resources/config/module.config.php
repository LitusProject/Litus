<?php
return array(
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'admin_article' => 'CudiBundle\Controller\Admin\ArticleController',
                'admin_booking' => 'CudiBundle\Controller\Admin\BookingController',
                'admin_stock' => 'CudiBundle\Controller\Admin\StockController',
                'admin_order' => 'CudiBundle\Controller\Admin\OrderController',
            ),
            'admin_article' => array(
            	'parameters' => array(
            		'filePath' => realpath('data/cudi/files'),
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
                'route'    => '/admin/article[/:action[/:id[/:confirm]]]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
    	        	'confirm' => '[01]',
                ),
                'defaults' => array(
                    'controller' => 'admin_article',
                    'action'     => 'manage',
                ),
            ),
        ),
        'admin_article_pagination' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/article/manage[/:page]',
                'constraints' => array(
                	'page'	  => '[0-9]*',
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
                'route'    => '/admin/article/search/[/:field[/:string]]',
                'constraints' => array(
                    'field' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                 'route'    => '/admin/booking[/:action[/:id[/:confirm]]]',
                 'constraints' => array(
                     'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                     'id'      => '[0-9]*',
    	        	 'confirm' => '[01]',
                 ),
                 'defaults' => array(
                     'controller' => 'admin_booking',
                     'action'     => 'manage',
                 ),
             ),
         ),
         'admin_article_pagination' => array(
             'type'    => 'Zend\Mvc\Router\Http\Segment',
             'options' => array(
                 'route'    => '/admin/booking/manage[/:page]',
                 'constraints' => array(
                 	'page'	  => '[0-9]*',
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
                 'route'    => '/admin/booking/search/[/:field[/:string]]',
                 'constraints' => array(
                     'field' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                  'route'    => '/admin/stock[/:action[/:id]]',
                  'constraints' => array(
                      'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'id'      => '[0-9]*',
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
                  'route'    => '/admin/stock/search/[/:field[/:string]]',
                  'constraints' => array(
                      'field' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                   'route'    => '/admin/order[/:action[/:id]]',
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
    ),
);