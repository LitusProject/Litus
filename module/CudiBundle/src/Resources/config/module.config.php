<?php
return array(
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'cudibundle_article_admin' => 'CudiBundle\Controller\ArticleAdminController',
            )
        ),
    ),
    'routes' => array(
        'cudibundle_article_admin' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/article/[/:action]',
                'constraints' => array(
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'cudibundle_article_admin',
                    'action'     => 'index',
                ),
            ),
        ),
    ),
);