<?php
return array(
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'admin_article' => 'CudiBundle\Controller\Admin\ArticleController',
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
                'route'    => '/admin/article[/:action]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'admin_article',
                    'action'     => 'add',
                ),
            ),
        ),
    ),
);