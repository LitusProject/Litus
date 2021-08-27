<?php

namespace CommonBundle\Form\Admin\FAQ;

/**
 * Edit a faq.
 */
class Edit extends \CommonBundle\Form\Admin\FAQ\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'faq_edit');
    }
}
