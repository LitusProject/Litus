<?php

namespace NewsBundle\Form\Admin\News;

/**
 * Edit News
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \NewsBundle\Form\Admin\News\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'news_edit');
    }
}
