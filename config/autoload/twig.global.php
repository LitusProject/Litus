<?php

use Twig\Extension\DebugExtension;

if (getenv('APPLICATION_ENV') != 'development') {
    return array(
        'zend_twig' => array(
            'environment' => array(
                'cache' => 'data/cache/twig',
            ),
        ),
    );
}

return array(
    'zend_twig' => array(
        'environment' => array(
            'cache' => 'data/cache/twig',
            'debug' => true,
        ),
        'extensions' => array(
            DebugExtension::class,
        ),
    ),
);
