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

namespace CudiBundle\Form\Admin\Stock\Delivery;

/**
 * Add Delivery
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string
     */
    private $barcodePrefix = '';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'deliveryForm');

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'add_with_virtual_order',
            'attributes' => array(
                'id' => 'addWithVirtualOrder',
            ),
        ));

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'article',
            'label'      => 'Article',
            'required'   => true,
            'value'      => $this->barcodePrefix,
            'attributes' => array(
                'id'    => 'article',
                'style' => 'width: 400px;',
            ),
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'typeahead_sale_article'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'number',
            'label'      => 'Number',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'id'           => 'delivery_number',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name'    => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'stock_add', 'add', array('id' => 'stock_add'));
    }

    /**
     * @param  string $barcodePrefix
     * @return self
     */
    public function setBarcodePrefix($barcodePrefix)
    {
        $this->barcodePrefix = $barcodePrefix;

        return $this;
    }
}
