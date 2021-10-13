<?php

namespace CudiBundle\Form\Admin\Sale\Article\Barcode;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'barcode',
                'label'      => 'Barcode',
                'required'   => true,
                'attributes' => array(
                    'class' => 'disableEnter',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'SaleArticleBarcodeUnique',
                            ),
                            array(
                                'name'    => 'Barcode',
                                'options' => array(
                                    'adapter'     => 'Ean12',
                                    'useChecksum' => false,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'article_add');
    }
}
