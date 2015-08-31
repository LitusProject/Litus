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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Form\Admin\Product;

/**
 * Add Product
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Product';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type' => 'text',
            'name' => 'name',
            'label' => 'Name',
            'required' => true,
            'options' => array(
                'input' => array(
                    'validators' => array(),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'sell_price',
            'label' => 'Sell Price',
            'required' => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'price'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'checkbox',
            'name' => 'available',
            'label' => 'Available',
            'attributes' => array(
                'data-help' => 'Enabling this option will allow clients to reserve this article.',
            ),
        ));

        $this->addSubmit('Add', 'product_add');
    }
}
