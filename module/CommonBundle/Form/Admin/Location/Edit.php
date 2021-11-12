<?php

namespace CommonBundle\Form\Admin\Location;

/**
 * Edit Location
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Location\Add
{
    public function init()
    {
        parent::init();

        $this->remove('add')
            ->addSubmit('Save', 'location_edit', 'edit');
    }
}
