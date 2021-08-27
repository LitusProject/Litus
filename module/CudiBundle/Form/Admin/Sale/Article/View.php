<?php

namespace CudiBundle\Form\Admin\Sale\Article;

/**
 * View Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class View extends \CudiBundle\Form\Admin\Sale\Article\Add
{
    public function init()
    {
        parent::init();

        foreach ($this->getElements() as $element) {
            $element->setAttribute('disabled', true);
        }

        $this->remove('submit');
    }
}
