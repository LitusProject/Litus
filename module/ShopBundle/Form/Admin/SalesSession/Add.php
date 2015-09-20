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

namespace ShopBundle\Form\Admin\SalesSession;

/**
 * Add SalesSession
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\SalesSession';

    /**
	 * @var Product[]
	 */
    protected $products = array();

    public function init()
    {
        parent::init();

        $this->add(array(
            'type' => 'datetime',
            'name' => 'start_date',
            'label' => 'Start Date',
            'required' => true,
        ));

        $this->add(array(
            'type' => 'datetime',
            'name' => 'end_date',
            'label' => 'End Date',
            'required' => true,
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'date_compare',
                            'options' => array(
                                'first_date' => 'start_date',
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'checkbox',
            'name' => 'reservations_possible',
            'label' => 'Reservations Possible',
            'attributes' => array(
                'data-help' => 'Enabling this option will allow clients to reserve articles for this sales session.',
                'value' => true,
            ),
        ));

        foreach ($this->products as $product) {
            $this->add(array(
                'type' => 'number',
                'name' => $product->getId() . '-quantity',
                'options' => array(
                    'label' => $product->getName(),
                ),
                'attributes' => array(
                    'min' => '0',
                    'max' => '100',
                    'value' => 0,
                ),
            ));
        }

        $this->add(array(
            'type' => 'textarea',
            'name' => 'remarks',
            'label' => 'Remarks',
            'required' => false,
            'attributes' => array(
                'rows' => 5,
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'add');
    }

    /**
	 * @param $products
	 */
    public function setProducts($products)
    {
        $this->products = $products;
    }
}
