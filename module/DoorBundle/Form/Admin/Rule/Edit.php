<?php

namespace DoorBundle\Form\Admin\Rule;

/**
 * Edit Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \DoorBundle\Form\Admin\Rule\Add
{
    public function init()
    {
        parent::init();

        $this->remove('academic');

        $this->remove('submit')
            ->addSubmit('Save', 'rule_edit');
    }
}
