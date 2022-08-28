<?php

namespace CudiBundle\Form\Admin\Sale\Session\Message;

/**
 * Edit Message
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Edit extends \CudiBundle\Form\Admin\Sale\Session\Message\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'message_edit');
    }
}
