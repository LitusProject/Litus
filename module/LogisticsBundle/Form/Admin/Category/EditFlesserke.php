<?php

namespace LogisticsBundle\Form\Admin\Category;

use LogisticsBundle\Entity\FlesserkeCategory;

/**
 * The form used to edit an existing FlesserkeCategory.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class EditFlesserke extends \LogisticsBundle\Form\Admin\Category\AddFlesserke
{
    /**
     * @var FlesserkeCategory
     */
    private FlesserkeCategory $category;

    public function init(): void
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Update', 'edit');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract($this->category));
    }

    /**
     * @param FlesserkeCategory $category
     * @return self
     */
    public function setCategory(FlesserkeCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
