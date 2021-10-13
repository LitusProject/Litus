<?php

namespace PageBundle\Form\Admin\Category;

/**
 * Edit Category
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \PageBundle\Form\Admin\Category\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'category_edit');
    }
}
