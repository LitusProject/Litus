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

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder,
    PageBundle\Component\Validator\Name as PageNameValidator,
    PageBundle\Entity\Nodes\Page;

/**
 * Edit a page.
 */
class Edit extends Add
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param mixed $opts The validator's options
	 */
    public function __construct(EntityManager $entityManager, Page $page, $opts = null)
    {
        parent::__construct($entityManager, $opts);
        
        $form = $this->getSubForm('tab-content');
        
        foreach ($this->_getLanguages() as $language) {
            $title = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('title_' . $language->getAbbrev());
            $title->clearValidators();
            $title->addValidator(new PageNameValidator($entityManager, $page->getTranslation($language)));
        }
        
        $this->removeElement('submit');
        
        $field = new Submit('submit');
        $field->setLabel('Save');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
        
        $this->populateFromPage($page);
    }
}