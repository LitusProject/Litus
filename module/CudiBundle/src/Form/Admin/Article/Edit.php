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
 
namespace CudiBundle\Form\Admin\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Article\Add
{
    public function __construct(EntityManager $entityManager, Article $article, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->removeElement('submit');
        
        foreach($this->getDisplayGroup('subject_form')->getElements() as $element)
            $this->removeElement($element->getName());
        $this->removeDisplayGroup('subject_form');
        
        $field = new Submit('submit');
        $field->setLabel('Save')
                ->setAttrib('class', 'article_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromArticle($article);
    }
}
