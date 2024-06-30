<?php

namespace LogisticsBundle\Form\Admin\Category;

use LogisticsBundle\Entity\InventoryCategory;

/**
 * The form used to edit an existing InventoryCategory.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class EditInventory extends \LogisticsBundle\Form\Admin\Category\AddInventory
{
    /**
     * @var InventoryCategory
     */
    private InventoryCategory $category;

    public function init(): void
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Update', 'edit');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract($this->category));
    }

    /**
     * @param InventoryCategory $category
     * @return self
     */
    public function setCategory(InventoryCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
