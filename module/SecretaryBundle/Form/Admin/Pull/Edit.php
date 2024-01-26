<?php

namespace SecretaryBundle\Form\Admin\Pull;

class Edit extends \SecretaryBundle\Form\Admin\Pull\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'pull_edit');
    }
}
