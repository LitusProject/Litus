<?php

namespace LogisticsBundle\Form\Admin\Article;

/**
 * This form allows the user to edit the article.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Unit\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'article_edit');
    }
}
