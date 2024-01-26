<?php

namespace ShopBundle\Form\Admin\SalesSession;

/**
 * Add SalesSession
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Session';

    /**
     * @var array
     */
    protected $products = array();

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'final_reservation_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'final_reservation_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'datetime',
                'name'  => 'final_reservation_date',
                'label' => 'Final Reservation Date',
            )
        );

        $this->add(
            array(
                'type'       => 'number',
                'name'       => 'rewards_amount',
                'label'      => 'Rewards Amount',
                'attributes' => array(
                    'min'   => '0',
                    'max'   => '10',
                    'value' => '3',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'reservations_possible',
                'label'      => 'Reservations Possible',
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow clients to reserve articles for this sales session.',
                    'value'     => true,
                ),
            )
        );

        foreach ($this->products as $product) {
            $this->add(
                array(
                    'type'       => 'number',
                    'name'       => $product->getId() . '-quantity',
                    'options'    => array(
                        'label' => $product->getName(),
                    ),
                    'attributes' => array(
                        'min'   => '0',
                        'max'   => '100',
                        'value' => $product->getDefaultAmount() ? $product->getDefaultAmount() : 0,
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'remarks',
                'label'      => 'Remarks',
                'required'   => false,
                'attributes' => array(
                    'rows' => 5,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param array $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }
}
