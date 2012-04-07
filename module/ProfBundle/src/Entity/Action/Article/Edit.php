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
 
namespace ProfBundle\Entity\Action\Article;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action\Article\Edit")
 * @Table(name="prof.action_article_edit")
 */
class Edit extends \ProfBundle\Entity\Action
{
	/**
	 * @var integer The ID of this article edit action
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of this action
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var array The items
	 *
	 * @OneToMany(targetEntity="ProfBundle\Entity\Action\Article\Edit\Item", mappedBy="action")
	 */
	private $editItems;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\Article $article
     */
    public function __construct(Person $person, Article $article)
    {
        parent::__construct($person);
    	$this->article = $article;
    }
    
    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return string
     */
    public function getEntity()
    {
        return 'article';
    }
    
    /**
     * @return string
     */
    public function getAction()
    {
        return 'edit';
    }
}
