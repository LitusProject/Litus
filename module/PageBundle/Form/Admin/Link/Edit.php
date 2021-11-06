<?php

namespace PageBundle\Form\Admin\Link;

/**
 * Edit Link
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \PageBundle\Form\Admin\Link\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'link_edit');
    }
}
