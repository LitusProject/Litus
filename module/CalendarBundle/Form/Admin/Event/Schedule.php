<?php

namespace CalendarBundle\Form\Admin\Event;

/**
 * Add multiple standardized shifts at once; simplified version of add
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Schedule extends \CalendarBundle\Form\Admin\Event\Add
{
    protected $hydrator = 'CalendarBundle\Hydrator\Node\Event';

    public function init()
    {
        parent::init();

        $this->remove('is_hidden')
            ->remove('is_career')
            ->remove('is_international');
    }
}

