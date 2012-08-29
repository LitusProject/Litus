<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Form\Admin\Page;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Nodes\Page,
    Zend\Form\Element\Multiselect,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\TextArea;

/**
 * Add Page
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->addElement($tabs);

        $tabContent = new TabContent();

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new TitleValidator($entityManager));

            if ($language->getAbbrev() == \Locale::getDefault())
                $field->setRequired();

            $pane->addElement($field);

            $field = new Textarea('content_' . $language->getAbbrev());
            $field->setLabel('Content')
                ->setAttrib('rows', 20)
                ->setDecorators(array(new FieldDecorator()));

            if ($language->getAbbrev() == \Locale::getDefault())
                $field->setRequired();

            $pane->addElement($field);

            $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
        }

        $this->addSubForm($tabContent, 'tab_content');

        $field = new Select('category');
        $field->setLabel('Category')
            ->setRequired()
            ->setMultiOptions($this->_createCategoriesArray())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Multiselect('edit_roles');
        $field->setLabel('Edit Roles')
            ->setRequired()
            ->setMultiOptions($this->_createEditRolesArray())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('parent');
        $field->setLabel('Parent')
            ->setMultiOptions($this->_createPagesArray())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'page_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
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

        $categoryOptions = array();
        foreach($categories as $category)
            $categoryOptions[$category->getId()] = $category->getName();

        return $categoryOptions;
    }

    private function _createPagesArray()
    {
        $pages = $this->_entityManager
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findAll();

        $pageOptions = array(
            '' => ''
        );
        foreach($pages as $page)
            $pageOptions[$page->getId()] = $page->getTitle();

        return $pageOptions;
    }

    private function _createEditRolesArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }
        return $rolesArray;
    }
}
