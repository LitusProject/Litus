<?php

namespace SecretaryBundle\Form\Admin\Pull;

class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SecretaryBundle\Hydrator\Pull';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'text',
                'name' => 'study_nl',
                'label' => 'Study NL',
                'attributes' => array(
                    'id' => 'study_nl',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'study_en',
                'label' => 'Study EN',
                'attributes' => array(
                    'id' => 'study_en',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'available',
                'label' => 'Available to book',
                'attributes' => array(
                    'id' => 'available'
                ),
            )
        );

        $this->addSubmit('Add', 'pull_add');
    }
}