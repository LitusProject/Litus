<?php

namespace PageBundle\Form\Admin\Frame;

/**
 * Edit a CategoryPage.
 */
class Edit extends \PageBundle\Form\Admin\Frame\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'page_edit');
    }
}
