<?php

namespace CudiBundle\Form\Admin\Article;

/**
 * Edit Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('subject_form');

        $this->remove('submit')
            ->addSubmit('Save', 'article_edit');

        $this->bind($this->article);
    }
}
