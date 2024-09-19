<?php

namespace MailBundle\Form\Admin\Preference;

/**
 * Edit Preference
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'preference_edit');
    }
}
