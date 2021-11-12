<?php

namespace CudiBundle\Form\Prof\Article;

/**
 * Edit Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Prof\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('subject');

        $this->remove('submit')
            ->addSubmit('Save', 'btn btn-primary', 'submit');

        $this->remove('draft')
            ->addSubmit('Save As Draft', 'btn btn-info', 'draft');
    }
}
