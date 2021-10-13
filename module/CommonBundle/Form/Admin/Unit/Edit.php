<?php

namespace CommonBundle\Form\Admin\Unit;

/**
 * Edit Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Unit\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'unit_edit');
    }
}
