<?php

namespace SportBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'universityidentification' => Component\Validator\UniversityIdentification::class,
                'universityIdentification' => Component\Validator\UniversityIdentification::class,
                'UniversityIdentification' => Component\Validator\UniversityIdentification::class,
            ),
        ),
    )
);
