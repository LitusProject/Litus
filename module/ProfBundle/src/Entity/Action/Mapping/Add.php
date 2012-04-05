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
 
namespace ProfBundle\Entity\Action\Mapping;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\ArticleSubjectMap;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action\Mapping\Add")
 * @Table(name="prof.action_mapping_add")
 */
class Add extends \ProfBundle\Entity\Action
{
	/**
	 * @var integer The ID of this article remove action
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
	 * @var \SyllabusBundle\Entity\Subject The article of this action
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\Article $article
     * @param \SyllabusBundle\Entity\Subject $subject
     */
    public function __construct(Person $person, Article $article, Subject $subject)
    {
        parent::__construct($person);
    	$this->article = $article;
    	$this->subject = $subject;
    }
    
    /**
     * @return \CudiBundle\Entity\ArticleSubjectMap
     */
    public function getArticleSubjectMap()
    {
        return $this->mapping;
    }
    
    /**
     * @return string
     */
    public function getEntity()
    {
        return 'mapping';
    }
    
    /**
     * @return string
     */
    public function getAction()
    {
        return 'add';
    }
}
