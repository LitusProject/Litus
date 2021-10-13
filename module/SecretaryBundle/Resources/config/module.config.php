<?php

namespace SecretaryBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'cancelregistration' => Component\Validator\CancelRegistration::class,
                'cancelRegistration' => Component\Validator\CancelRegistration::class,
                'CancelRegistration' => Component\Validator\CancelRegistration::class,
            ),
        ),
    )
);
