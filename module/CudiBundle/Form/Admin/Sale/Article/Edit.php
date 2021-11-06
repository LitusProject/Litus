<?php

namespace CudiBundle\Form\Admin\Sale\Article;

/**
 * Edit Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sale\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'article_edit');

        $membershipArticles = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        if (in_array($this->article->getId(), $membershipArticles)) {
            $this->get('bookable')->setAttribute('disabled', true);
            $this->get('unbookable')->setAttribute('disabled', true);
        }
    }
}
