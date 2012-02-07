<?php
return array(
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
            ),
            'doctrine_config' => array(
                'parameters' => array(
                	'entityPaths' => array(
                		'syllabusbundle' => __DIR__ . '/../../Entity',
                	),
                ),
            ), 
        ),
    ),
    'routes' => array(
    ),
);