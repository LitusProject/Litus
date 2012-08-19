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
    Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Title as TitleValidator,
    PageBundle\Entity\Nodes\Page,
    Zend\Form\Element\Submit;

/**
 * Edit a page.
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, Page $page, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        foreach ($this->getLanguages() as $language) {
            $title = $this->getSubForm('tab_content')
                ->getSubForm('tab_' . $language->getAbbrev())
                ->getElement('title_' . $language->getAbbrev());

            $title->clearValidators();
            $title->addValidator(
                new TitleValidator($entityManager, $page->getName())
            );
        }

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'category_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

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

        $data['parent'] = null !== $page->getParent() ? $page->getParent()->getName() : '';

        $this->populate($data);
    }
}
