<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
                'FeatureImportanceConstraint' => Component\Validator\FeatureImportanceConstraint::class,
                'featureImportanceConstraint' => Component\Validator\FeatureImportanceConstraint::class,
            ),
        ),
    )
);
