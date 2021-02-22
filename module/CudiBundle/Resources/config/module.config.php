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
                'hasbought'                    => Component\Validator\Sale\HasBought::class,
                'hasBought'                    => Component\Validator\Sale\HasBought::class,
                'HasBought'                    => Component\Validator\Sale\HasBought::class,
                'salearticlebarcodeunique'     => Component\Validator\Sale\Article\Barcode\Unique::class,
                'saleArticleBarcodeUnique'     => Component\Validator\Sale\Article\Barcode\Unique::class,
                'SaleArticleBarcodeUnique'     => Component\Validator\Sale\Article\Barcode\Unique::class,
                'salearticlediscountexits'     => Component\Validator\Sale\Article\Discount\Exists::class,
                'saleArticleDiscountExits'     => Component\Validator\Sale\Article\Discount\Exists::class,
                'SaleArticleDiscountExits'     => Component\Validator\Sale\Article\Discount\Exists::class,
                'salearticlerestrictionexists' => Component\Validator\Sale\Article\Restriction\Exists::class,
                'saleArticleRestrictionExists' => Component\Validator\Sale\Article\Restriction\Exists::class,
                'SaleArticleRestrictionExists' => Component\Validator\Sale\Article\Restriction\Exists::class,
                'salesessionrestrictionexists' => Component\Validator\Sale\Session\Restriction\Exists::class,
                'saleSessionRestrictionExists' => Component\Validator\Sale\Session\Restriction\Exists::class,
                'SaleSessionRestrictionExists' => Component\Validator\Sale\Session\Restriction\Exists::class,
                'salesessionrestrictionvalues' => Component\Validator\Sale\Session\Restriction\Values::class,
                'saleSessionRestrictionValues' => Component\Validator\Sale\Session\Restriction\Values::class,
                'SaleSessionRestrictionValues' => Component\Validator\Sale\Session\Restriction\Values::class,
                'typeaheadarticle'             => Component\Validator\Typeahead\Article::class,
                'typeaheadArticle'             => Component\Validator\Typeahead\Article::class,
                'TypeaheadArticle'             => Component\Validator\Typeahead\Article::class,
                'typeaheadsalearticle'         => Component\Validator\Typeahead\Sale\Article::class,
                'typeaheadSaleArticle'         => Component\Validator\Typeahead\Sale\Article::class,
                'TypeaheadSaleArticle'         => Component\Validator\Typeahead\Sale\Article::class,
                'TypeaheadRetail'         => Component\Validator\Typeahead\Retail::class,
                'typeaheadRetail'         => Component\Validator\Typeahead\Retail::class,
                'typeaheadretail'         => Component\Validator\Typeahead\Retail::class,
                'MaximalRetailPrice'         => Component\Validator\RetailPrice::class,
                'maximalRetailPrice'         => Component\Validator\RetailPrice::class,
                'maximalretailprice'         => Component\Validator\RetailPrice::class,
            ),
        ),
    )
);
