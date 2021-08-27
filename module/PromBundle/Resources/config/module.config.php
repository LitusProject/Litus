<?php

namespace PromBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('prom'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'busselected'     => Component\Validator\BusSelected::class,
                'busSelected'     => Component\Validator\BusSelected::class,
                'BusSelected'     => Component\Validator\BusSelected::class,
                'busseats'        => Component\Validator\BusSeats::class,
                'busSeats'        => Component\Validator\BusSeats::class,
                'BusSeats'        => Component\Validator\BusSeats::class,
                'codeemail'       => Component\Validator\CodeEmail::class,
                'codeEmail'       => Component\Validator\CodeEmail::class,
                'CodeEmail'       => Component\Validator\CodeEmail::class,
                'codeexists'      => Component\Validator\CodeExists::class,
                'codeExists'      => Component\Validator\CodeExists::class,
                'CodeExists'      => Component\Validator\CodeExists::class,
                'codeused'        => Component\Validator\CodeUsed::class,
                'codeUsed'        => Component\Validator\CodeUsed::class,
                'CodeUsed'        => Component\Validator\CodeUsed::class,
                'passengerexists' => Component\Validator\PassengerExists::class,
                'passengerExists' => Component\Validator\PassengerExists::class,
                'PassengerExists' => Component\Validator\PassengerExists::class,
            ),
        ),
    )
);
