<?php

namespace ApiBundle\Form\Admin\Key;

/**
 * Edit Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \ApiBundle\Form\Admin\Key\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'key_edit');
    }
}
