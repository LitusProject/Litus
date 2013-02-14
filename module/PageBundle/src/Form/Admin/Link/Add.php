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

namespace PageBundle\Form\Admin\Link;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    PageBundle\Entity\Category,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Link
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('name_' . $language->getAbbrev());
            $field->setLabel('Name')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Text('url_' . $language->getAbbrev());
            $field->setLabel('URL')
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

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'link_add');
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
            throw new \RuntimeException('There needs to be at least one category before you can add a link');

        $categoryOptions = array();
        foreach($categories as $category)
            $categoryOptions[$category->getId()] = $category->getName();

        asort($categoryOptions);

        return $categoryOptions;
    }

    private function _createPagesArray(Category $category)
    {
        $pages = $this->_entityManager
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findByCategory($category);

        $pageOptions = array(
            '' => ''
        );
        foreach($pages as $page)
            $pageOptions[$page->getId()] = $page->getTitle();

        return $pageOptions;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'name_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'url_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Uri'),
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

        return $inputFilter;
    }
}
