<?php

namespace ShopBundle\Form\Admin\Session\Message;

/**
 * Edit Message
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Edit extends \ShopBundle\Form\Admin\Session\Message\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'message_edit');
    }
}
