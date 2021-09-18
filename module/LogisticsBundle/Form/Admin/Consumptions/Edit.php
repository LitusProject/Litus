<?php

namespace LogisticsBundle\Form\Admin\Consumptions;

class Edit extends \LogisticsBundle\Form\Admin\Consumptions\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'consumptions_edit');
    }
}
