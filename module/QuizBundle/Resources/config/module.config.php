<?php

namespace QuizBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'roundnumber' => Component\Validator\RoundNumber::class,
                'roundNumber' => Component\Validator\RoundNumber::class,
                'RoundNumber' => Component\Validator\RoundNumber::class,
                'teamnumber'  => Component\Validator\TeamNumber::class,
                'teamNumber'  => Component\Validator\TeamNumber::class,
                'TeamNumber'  => Component\Validator\TeamNumber::class,
            ),
        ),
    )
);
