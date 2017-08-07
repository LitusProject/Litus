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

namespace CudiBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('isic', 'site', 'supplier', 'prof', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'invokables' => array(
                'sale_article_barcode_unique'     => 'CudiBundle\Component\Validator\Sale\Article\Barcode\Unique',
                'sale_article_discount_exits'     => 'CudiBundle\Component\Validator\Sale\Article\Discount\Exists',
                'sale_article_restriction_exists' => 'CudiBundle\Component\Validator\Sale\Article\Restriction\Exists',
                'sale_session_restriction_exists' => 'CudiBundle\Component\Validator\Sale\Session\Restriction\Exists',
                'sale_session_restriction_values' => 'CudiBundle\Component\Validator\Sale\Session\Restriction\Values',
                'sale_has_bought'                 => 'CudiBundle\Component\Validator\Sale\HasBought',
                'typeahead_sale_article'          => 'CudiBundle\Component\Validator\Typeahead\Sale\Article',
                'typeahead_article'               => 'CudiBundle\Component\Validator\Typeahead\Article',
            ),
        ),
    )
);
