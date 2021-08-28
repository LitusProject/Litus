<?php

namespace SecretaryBundle\Form\Admin\WorkingGroup;

class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SecretaryBundle\Hydrator\WorkingGroup\Academic';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Account',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'user_add');
    }
}
