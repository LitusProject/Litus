<?php

namespace CudiBundle\Form\Admin\Prof\Article;

/**
 * Confirm Article add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CudiBundle\Form\Admin\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('subject_form');

        $this->remove('submit')
            ->addSubmit('Confirm', 'article_add');

        $this->bind($this->article);
    }
}
