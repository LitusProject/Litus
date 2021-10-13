<?php

namespace TicketBundle\Form\Admin\Consumptions;

class Edit extends \TicketBundle\Form\Admin\Consumptions\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'consumptions_edit');
    }
}
