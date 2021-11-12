<?php

namespace PageBundle\Form\Admin\Page;

/**
 * Edit a page.
 */
class Edit extends \PageBundle\Form\Admin\Page\Add
{
    public function init()
    {
        parent::init();

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            $parentField = $this->get('parent_' . $category->getId());
            $parentField->setValueOptions(
                $this->createPagesArray(
                    $category,
                    $this->getPage()->getCategory()->getId() == $category->getId() ? $this->getPage()->getTitle() : ''
                )
            );
        }

        $this->remove('submit')
            ->addSubmit('Save', 'category_edit');
    }
}
