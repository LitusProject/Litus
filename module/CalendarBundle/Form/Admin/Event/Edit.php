<?php

namespace CalendarBundle\Form\Admin\Event;

/**
 * Edit an event.
 */
class Edit extends \CalendarBundle\Form\Admin\Event\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'calendar_edit');
    }
}
