<?php

namespace CudiBundle\Form\Admin\Article;

/**
 * Duplicate of Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Duplicate extends \CudiBundle\Form\Admin\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('subject_form');

        $articleFieldset = $this->get('article');
        $articleFieldset->get('type')
            ->setAttribute('disabled', true);

        if ($this->article->getType() == 'common') {
            $articleFieldset->remove('type');
        }

        $this->remove('submit')
            ->addSubmit('Add', 'article_add');

        // Don't bind to the article, but extract its data
        $this->setData($this->getHydrator()->extract($this->article));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        unset($specs['article']['type']);

        return $specs;
    }
}
