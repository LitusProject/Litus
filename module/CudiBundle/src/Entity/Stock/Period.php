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
 
namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Article,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\Period")
 * @Table(name="cudi.stock_period")
 */
class Period
{
	/**
	 * @var integer The ID of the period
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person who created this period
	 *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
	private $person;
	
	/**
	 * @var \DateTime The start time of the period
	 *
	 * @Column(name="start_date", type="datetime")
	 */
	private $startDate;
	
	/**
	 * @var \DateTime The end time of the period
	 *
	 * @Column(name="end_date", type="datetime", nullable=true)
	 */
	private $endDate;
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
	
	/**
	 * @param \CommonBundle\Entity\Users\Person $person The person who created this period
	 */
	public function __construct(Person $person)
	{
		$this->person = $person;
		$this->startDate = new DateTime();
	}
	
	/**
	 * Get the id of this delta
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Get the person of this delta
	 *
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * Get the start date of this period
	 *
	 * @return \DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	/**
	 * Get the end date of this period
	 *
	 * @return \DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}
	
	/**
	 * @return boolean
	 */
	public function isOpen()
	{
	    return $this->endDate == null;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\Period
	 */
	public function close()
	{
	    $this->endDate = new DateTime();
	    return $this;
	}
	
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 *
	 * @return \CudiBundle\Entity\Stock\Period
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
	    $this->_entityManager = $entityManager;
	    return $this;
	}
	
	/**
	 * @param \CudiBundle\Entity\Article
	 *
	 * @return integer
	 */
	public function getNbDelivered(Article $article)
	{
	    return $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Stock\Period')
	        ->getNbDeliveredByArticle($this, $article);
	}
	
	/**
	 * @param \CudiBundle\Entity\Article
	 *
	 * @return integer
	 */
	public function getNbOrdered(Article $article)
	{
	    return $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Stock\Period')
	        ->getNbOrderedByArticle($this, $article);
	}
	
	/**
	 * @param \CudiBundle\Entity\Article
	 *
	 * @return integer
	 */
	public function getNbSold(Article $article)
	{
	    return $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Stock\Period')
	        ->getNbSoldByArticle($this, $article);
	}
}
