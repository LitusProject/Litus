<?php

namespace TicketBundle\Form\Admin\Consumptions;

use CommonBundle\Entity\User\Person;
use TicketBundle\Entity\Consumptions;

/**
 * Add first Consumptions
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'TicketBundle\Hydrator\Consumptions';

    protected $consumptions;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
//                            array(
//                                'name'    => 'EntryAcademic',
//                                'options' => array(
//                                    'list' => $this->getList(),
//                                ),
//                            ),
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'number_of_consumptions',
                'label' => 'Number of Consumptions',
//                'value' => 0,
                'required' => true,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                            array(
                                'name' => 'greaterthan',
                                'options' => array(
                                    'min' => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'consumptions_add');

        if ($this->consumptions !== null) {
            $this->bind($this->consumptions);
        }
    }

    public function setConsumptions(Consumptions $consumptions)
    {
        $this->consumptions = $consumptions;

        return $this;
    }
}