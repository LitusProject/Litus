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

use Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Nodes\Page,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit;

/**
 * Edit a page.
 */
class Edit extends Add
{
    /**
     * @param \PageBundle\Entity\Nodes\Page
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

        $this->remove('parent');

        $field = new Select('parent');
        $field->setLabel('Parent')
            ->setAttribute('options', $this->_createPagesArray($page->getTitle()));
        $this->add($field);

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

        $data['parent'] = null !== $page->getParent() ? $page->getParent()->getId() : '';

        $this->setData($data);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
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
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
