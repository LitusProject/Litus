<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Form\Admin\Page;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Category,
    PageBundle\Entity\Node\Page as PageEntity;

/**
 * Add Page
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'PageBundle\Hydrator\Node\Page';

    /**
     * @var \PageBundle\Entity\Node\Page
     */
    private $_page;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'category',
            'label'      => 'Category',
            'required'   => true,
            'options'    => array(
                'options' => $this->_createCategoriesArray(),
            ),
        ));

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach ($categories as $category) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'parent_' . $category->getId(),
                'label'      => 'Parent',
                'attributes' => array(
                    'class' => 'parent',
                ),
                'options'    => array(
                    'options' => $this->createPagesArray($category),
                ),
            ));
        }

        $this->add(array(
            'type'       => 'select',
            'name'       => 'edit_roles',
            'label'      => 'Edit Roles',
            'required'   => true,
            'attributes' => array(
                'multiple' => true,
            ),
            'options'    => array(
                'options' => $this->_createEditRolesArray(),
            ),
        ));

        $this->addSubmit('Add', 'page_add');

        if (null !== $this->getPage())
            $this->bind($this->getPage());
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'       => 'text',
            'name'       => 'title',
            'label'      => 'Title',
            'required'   => $isDefault,
            'attributes' => array(
                'width' => '400px',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new TitleValidator($this->getEntityManager(), $this->getPage() ? $this->getPage()->getName() : ''),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'       => 'textarea',
            'name'       => 'content',
            'label'      => 'Content',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    private function _createCategoriesArray()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        if (empty($categories))
            throw new \RuntimeException('There needs to be at least one category before you can add a page');

        $categoryOptions = array();
        foreach($categories as $category)
            $categoryOptions[$category->getId()] = $category->getName();

        asort($categoryOptions);

        return $categoryOptions;
    }

    protected function createPagesArray(Category $category, $exclude = '')
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByCategory($category, array('name' => 'ASC'));

        $pageOptions = array(
            '' => ''
        );
        foreach ($pages as $page) {
            if ($page->getTitle() != $exclude)
                $pageOptions[$page->getId()] = $page->getTitle();
        }

        return $pageOptions;
    }

    private function _createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem())
                $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new \RuntimeException('There needs to be at least one role before you can add a page');

        return $rolesArray;
    }

    /**
     * @param \PageBundle\Entity\Node\Page
     * @return \PageBundle\Form\Admin\Page\Add
     */
    public function setPage(PageEntity $page)
    {
        $this->_page = $page;

        return $this;
    }

    /**
     * @return \PageBundle\Entity\Node\Page
     */
    public function getPage()
    {
        return $this->_page;
    }
}
