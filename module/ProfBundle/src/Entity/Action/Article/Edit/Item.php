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
 
namespace ProfBundle\Entity\Action\Article\Edit;

use Doctrine\ORM\EntityManager,
    ProfBundle\Entity\Action\Article\Edit;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action\Article\Edit\Item")
 * @Table(name="prof.action_article_edit_item")
 */
class Item
{
	/**
	 * @var integer The ID of this article edit action item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \ProfBundle\Entity\Action\Article\Edit The action of this edit item
	 *
	 * @ManyToOne(targetEntity="ProfBundle\Entity\Action\Article\Edit", inversedBy="items")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $action;
	
	/**
	 * @var string The field edited
	 *
	 * @Column(type="string")
	 */
	private $field;
	
	/**
	 * @var string The new value
	 *
	 * @Column(type="string")
	 */
	private $value;
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
    
    /**
     * @param \ProfBundle\Entity\Action\Article\Edit $action
     * @param string $field
     * @param string $value
     */
    public function __construct(Edit $action, $field, $value)
    {
    	$this->action = $action;
    	$this->field = $field;
    	$this->value = $value;
    }
    
    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * @return string
     */
    public function getFieldName()
    {
        switch ($this->field) {
            case 'title':
                return 'Title';
            case 'author':
                return 'Author';
            case 'publisher':
                return 'Publisher';
            case 'year_published':
                return 'Year Published';
            case 'binding':
                return 'Binding';
            case 'rectoverso':
                return 'Recto Verso';
        }
    }
    
    /**
     * @return string
     */
    public function getCurrentValue()
    {
        switch ($this->field) {
            case 'title':
                return $this->action->getArticle()->getTitle();
            case 'author':
                return $this->action->getArticle()->getMetaInfo()->getAuthors();
            case 'publisher':
                return $this->action->getArticle()->getMetaInfo()->getPublishers();
            case 'year_published':
                return $this->action->getArticle()->getMetaInfo()->getYearPublished();
            case 'binding':
                return $this->action->getArticle()->getBinding()->getName();
            case 'rectoverso':
                return $this->action->getArticle()->isRectoVerso() ? 'Yes' : 'No';
        }
    }
    
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @return string
     */
    public function getNewValue()
    {
        switch ($this->field) {
            case 'title':
                return $this->value;
            case 'author':
                return $this->value;
            case 'publisher':
                return $this->value;
            case 'year_published':
                return $this->value;
            case 'binding':
                return $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
                    ->findOneById($this->value)
                    ->getName();
            case 'rectoverso':
                return $this->value ? 'Yes' : 'No';
        }
    }
    
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return \ProfBundle\Entity\Action\Article\Edit\Item
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }
}
