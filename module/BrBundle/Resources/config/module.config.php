<?php

namespace BrBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('corporate', 'career', 'cv', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'contractbullet'              => Component\Validator\ContractBullet::class,
                'contractBullet'              => Component\Validator\ContractBullet::class,
                'ContractBullet'              => Component\Validator\ContractBullet::class,
                'logotype'                    => Component\Validator\LogoType::class,
                'logoType'                    => Component\Validator\LogoType::class,
                'LogoType'                    => Component\Validator\LogoType::class,
                'companyname'                 => Component\Validator\CompanyName::class,
                'companyName'                 => Component\Validator\CompanyName::class,
                'CompanyName'                 => Component\Validator\CompanyName::class,
                'productname'                 => Component\Validator\ProductName::class,
                'productName'                 => Component\Validator\ProductName::class,
                'ProductName'                 => Component\Validator\ProductName::class,
            ),
        ),
    )
);
