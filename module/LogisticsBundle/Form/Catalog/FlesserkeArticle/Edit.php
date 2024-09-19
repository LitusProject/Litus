<?php

namespace LogisticsBundle\Form\Catalog\FlesserkeArticle;

use LogisticsBundle\Entity\FlesserkeArticle;

/**
 * The form used to edit an existing FlesserkeArticle.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Edit extends \LogisticsBundle\Form\Catalog\FlesserkeArticle\Add
{
    /**
     * @var FlesserkeArticle
     */
    private FlesserkeArticle $article;

    public function init(): void
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Update');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract($this->article));
    }

    /**
     * @param FlesserkeArticle $article
     * @return self
     */
    public function setArticle(FlesserkeArticle $article): self
    {
        $this->article = $article;

        return $this;
    }
}
