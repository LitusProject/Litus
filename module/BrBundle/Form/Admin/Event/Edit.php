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
            ->add(
                array(
                    'type'       => 'submit',
                    'name'       => 'event_edit',
                    'value'      => 'Save',
                    'attributes' => array(
                        'class' => 'mail_add',
                    ),
                )
            );
    }
}
