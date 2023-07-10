<?php

namespace PageBundle\Form\Admin\CategoryPage;

use PageBundle\Entity\Node\CategoryPage as CategoryPageEntity;
use RuntimeException;

/**
 * Add CategoryPage
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PageBundle\Hydrator\Node\CategoryPage';

    /**
     * @var CategoryPageEntity
     */
    private $categoryPage;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'select',
                'name' => 'category',
                'label' => 'Category',
                'required' => true,
                'attributes' => array(
                    'id' => 'category',
                    'options' => $this->createCategoriesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'select',
                'name' => 'edit_roles',
                'label' => 'Edit Roles',
                'required' => true,
                'attributes' => array(
                    'multiple' => true,
                ),
                'options' => array(
                    'options' => $this->createEditRolesArray(),
                ),
            )
        );

        $this->addSubmit('Add', 'page_add');

        if ($this->getCategoryPage() !== null) {
            $this->bind($this->getCategoryPage());
        }
    }

    private function createCategoriesArray()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        if (count($categories) == 0) {
            throw new RuntimeException('There needs to be at least one category before you can add a page');
        }

        $categoryOptions = array();
        foreach ($categories as $category) {
            $categoryOptions[$category->getId()] = $category->getName();
        }

        asort($categoryOptions);

        return $categoryOptions;
    }

    private function createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem()) {
                $rolesArray[$role->getName()] = $role->getName();
            }
        }

        if (count($rolesArray) == 0) {
            throw new RuntimeException('There needs to be at least one role before you can add a CategoryPage');
        }

        return $rolesArray;
    }

    /**
     * @param CategoryPageEntity
     * @return self
     */
    public function setCategoryPage(CategoryPageEntity $page)
    {
        $this->categoryPage = $page;

        return $this;
    }

    /**
     * @return CategoryPageEntity
     */
    public function getCategoryPage()
    {
        return $this->categoryPage;
    }
}
