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
 
namespace NewsBundle\Form\Admin\News;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder,
    NewsBundle\Entity\Nodes\News;

/**
 * Edit a page.
 */
class Edit extends Add
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param mixed $opts The validator's options
	 */
    public function __construct(EntityManager $entityManager, News $news, $opts = null)
    {
        parent::__construct($entityManager, $opts);
        
        $this->news = $news;
        
        $form = $this->getSubForm('tab-content');
        
        foreach ($this->_getLanguages() as $language) {
            $title = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('title_' . $language->getAbbrev());
            $title->clearValidators();
        }
        
        $this->removeElement('submit');
        
        $field = new Submit('submit');
        $field->setLabel('Save');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
        
        $this->populateFromNews($news);
    }
}