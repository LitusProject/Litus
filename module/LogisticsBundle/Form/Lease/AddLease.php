<?php

namespace LogisticsBundle\Form\Lease;

/**
 * The form used to add a new Lease.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class AddLease extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'typeahead',
                'name'    => 'leaseItem',
                'label'   => 'Item',
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadLease'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'leased_amount',
                'label'    => 'Amount',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Digits'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'leased_to',
                'label'      => 'Leased To',
                'required'   => true,
                'attributes' => array(
                    'id'           => 'leased_to',
                    'autocomplete' => false,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'leased_pawn',
                'label'    => 'Received Pawn',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'comment',
                'label'      => 'Comment',
                'attributes' => array(
                    'rows' => 3,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Lease', 'btn btn-primary', 'lease');
    }
}
