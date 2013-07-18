<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
    Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Node\Page,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit a page.
 */
class Edit extends Add
{
    /**
     * @param \PageBundle\Entity\Node\Page
     */
    private $_page;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Page $page, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_page = $page;

        $categories = $this->_entityManager
            ->getRepository('PageBundle\Entity\Category')
            ->findAll();

        foreach($categories as $category) {
            $this->remove('parent_' . $category->getId());

            $field = new Select('parent_' . $category->getId());
            $field->setLabel('Parent')
                ->setAttribute('class', 'parent')
                ->setAttribute('options', $this->createPagesArray($category, $page->getCategory()->getId() == $category->getId() ? $page->getTitle() : ''));
            $this->add($field);
        }

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'category_edit');
        $this->add($field);

        $this->_populateFromPage($page);
    }

    private function _populateFromPage(Page $page)
    {
        $data = array();
        foreach($this->getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $page->getTitle($language, false);
            $data['content_' . $language->getAbbrev()] = $page->getContent($language, false);
        }

        $data['category'] = $page->getCategory()->getId();

        $data['edit_roles'] = array();
        foreach ($page->getEditRoles() as $role)
            $data['edit_roles'][] = $role->getName();

        $data['parent_' . $page->getCategory()->getId()] = null !== $page->getParent() ? $page->getParent()->getId() : '';

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        foreach($this->getLanguages() as $language) {
            $inputFilter->remove('title_' . $language->getAbbrev());
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new TitleValidator($this->_entityManager, $this->_page->getName()),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
