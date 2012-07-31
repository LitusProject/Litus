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
 
namespace CudiBundle\Form\Admin\Sales\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sales\Article\Add
{
    public function __construct(EntityManager $entityManager, Article $article, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->removeElement('submit');
        
        $this->getElement('barcode')
            ->removeValidator('UniqueArticleBarcode')
            ->addValidator(new UniqueArticleBarcodeValidator($this->_entityManager, array($article->getId())));
        
        $field = new Submit('submit');
        $field->setLabel('Save')
                ->setAttrib('class', 'article_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromArticle($article);
    }
}
