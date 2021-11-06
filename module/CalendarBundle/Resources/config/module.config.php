<?php

namespace CalendarBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('common', 'validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'eventname' => Component\Validator\EventName::class,
                'eventName' => Component\Validator\EventName::class,
                'EventName' => Component\Validator\EventName::class,
            ),
        ),
    )
);
