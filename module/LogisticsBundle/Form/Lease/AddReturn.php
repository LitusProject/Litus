<?php

namespace LogisticsBundle\Form\Lease;

/**
 * The form used to register a returned item.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class AddReturn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'typeahead',
                'name'    => 'returnItem',
                'label'   => 'Item',
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'TypeaheadLease',
                                'options' => array(
                                    'must_be_leased' => true,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'returned_amount',
                'label'      => 'Amount',
                'required'   => true,
                'attributes' => array(
                    'id' => 'returned_amount',
                ),
                'options'    => array(
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
                'name'       => 'returned_by',
                'label'      => 'Returned By',
                'required'   => true,
                'attributes' => array(
                    'id'           => 'returned_by',
                    'autocomplete' => false,
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

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'returned_pawn',
                'label'      => 'Returned Pawn',
                'required'   => true,
                'attributes' => array(
                    'id' => 'returned_pawn',
                ),
                'options'    => array(
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
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Return', 'btn btn-primary', 'return');
    }
}
