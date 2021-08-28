<?php

namespace SportBundle\Form\Admin\Runner;

/**
 * Edit a runner of the queue.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SportBundle\Hydrator\Runner';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'runner_identification',
                'label'      => 'Runner Identification',
                'attributes' => array(
                    'autocomplete' => 'off',
                ),
                'options'    => array(
                    'input' => array(
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'edit');
    }
}
