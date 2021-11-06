<?php

namespace OnBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'SlugName' => Component\Validator\SlugName::class,
            ),
        ),
    )
);
