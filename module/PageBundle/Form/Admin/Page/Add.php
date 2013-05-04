<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Form\Admin\Page;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Category,
    PageBundle\Entity\Nodes\Page,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Page
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->setAttribute('data-upload', 'progress');

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());

            $pane->add($field);

            $field = new Textarea('content_' . $language->getAbbrev());
            $field->setLabel('Content')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault());

            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Select('category');
        $field->setLabel('Category')
            ->setRequired()
            ->setAttribute('options', $this->_createCategoriesArray());
        $this->add($field);

        $categories = $this->_entityManager
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach($categories as $category) {
            $field = new Select('parent_' . $category->getId());
            $field->setLabel('Parent')
                ->setAttribute('class', 'parent')
                ->setAttribute('options', $this->_createPagesArray($category));
            $this->add($field);
        }

        $field = new Select('edit_roles');
        $field->setLabel('Edit Roles')
            ->setRequired()
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createEditRolesArray());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'page_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    private function _createCategoriesArray()
    {
        $categories = $this->_entityManager
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

    protected function _createPagesArray(Category $category, $excludeTitle = '')
    {
        $pages = $this->_entityManager
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findByCategory($category);

        $pageOptions = array(
            '' => ''
        );
        foreach($pages as $page) {
            if ($page->getTitle() != $excludeTitle)
                $pageOptions[$page->getId()] = $page->getTitle();
        }

        asort($pageOptions);
        return $pageOptions;
    }

    private function _createEditRolesArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem())
                $rolesArray[$role->getName()] = $role->getName();
        }

        asort($rolesArray);

        return $rolesArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new TitleValidator($this->_entityManager),
                        ),
                    )
                )
            );

            if ($language->getAbbrev() !== \Locale::getDefault())
                continue;

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'content_' . $language->getAbbrev(),
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'category',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'edit_roles',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
