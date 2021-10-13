<?php

namespace FormBundle\Form\Admin\Group;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \FormBundle\Form\Admin\Group\Add
{
    public function init()
    {
        parent::init();

        $this->remove('start_form');

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
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
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'active',
                'label' => 'Active',
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'max',
                'label'   => 'Total Max Entries',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'non_member',
                'label' => 'Allow Entry Without Login',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'editable_by_user',
                'label' => 'Allow Users To Edit Their Info',
            )
        );

        $this->remove('submit')
            ->addSubmit('Save', 'form_edit');
    }
}
