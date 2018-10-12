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
            'aliases' => array(
                'sale_article_barcode_unique'     => Component\Validator\Sale\Article\Barcode\Unique::class,
                'sale_article_discount_exits'     => Component\Validator\Sale\Article\Discount\Exists::class,
                'sale_article_restriction_exists' => Component\Validator\Sale\Article\Restriction\Exists::class,
                'sale_session_restriction_exists' => Component\Validator\Sale\Session\Restriction\Exists::class,
                'sale_session_restriction_values' => Component\Validator\Sale\Session\Restriction\Values::class,
                'sale_has_bought'                 => Component\Validator\Sale\HasBought::class,
                'typeahead_sale_article'          => Component\Validator\Typeahead\Sale\Article::class,
                'typeahead_article'               => Component\Validator\Typeahead\Article::class,
            ),
        ),
    )
);
