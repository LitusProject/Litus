<?php

namespace PageBundle\Form\Admin\CategoryPage;

/**
 * Edit a CategoryPage.
 */
class Edit extends \PageBundle\Form\Admin\CategoryPage\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'category_edit');
    }
}
