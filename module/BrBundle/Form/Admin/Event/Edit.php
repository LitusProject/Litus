<?php

namespace BrBundle\Form\Admin\Event;

/**
 * Edit an event.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Edit extends \BrBundle\Form\Admin\Event\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'mail_edit');
    }
}
