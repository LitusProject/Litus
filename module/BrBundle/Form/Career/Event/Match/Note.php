<?php

namespace BrBundle\Form\Career\Event\Match;

class Note extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\Match';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'note',
                'label'   => 'Note',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

//        $this->addSubmit('Save', 'add_note');
    }
}