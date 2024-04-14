<?php

namespace LogisticsBundle\Form\Catalog\InventoryArticle;

use LogisticsBundle\Entity\InventoryArticle;

/**
 * The form used to edit an existing InventoryArticle.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Edit extends \LogisticsBundle\Form\Catalog\InventoryArticle\Add
{
    /**
     * @var InventoryArticle
     */
    private InventoryArticle $article;

    public function init(): void
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Update');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract($this->article));
    }

    /**
     * @param InventoryArticle $article
     * @return self
     */
    public function setOrder(InventoryArticle $article): self
    {
        $this->article = $article;

        return $this;
    }
}
