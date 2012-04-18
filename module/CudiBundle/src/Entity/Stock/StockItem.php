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

use CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager;
 
/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\StockItem")
 * @Table(name="cudi.stock_stockitem")
 */
class StockItem
{
	/**
	 * @var integer The ID of the stock item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of the stock item
	 *
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article", inversedBy="stockItem")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var integer The number in stock
	 *
	 * @Column(type="integer")
	 */
	private $numberInStock;
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
	
	/**
	 * @param \CudiBundle\Entity\Article $article
	 */
	public function __construct(Article $article)
	{
		$this->article = $article;
		$this->numberInStock = 0;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Return the article
	 * 
	 * @return \CudiBundle\Entity\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * Return the number in stock
	 * 
	 * @return integer
	 */
	public function getNumberInStock()
	{
		return $this->numberInStock;
	}
	
	/**
	 * @param integer $number The number in stock
	 *
	 * @param \CudiBundle\Entity\Stock\StockItem
	 */
	public function setNumberInStock($number)
	{
		$this->numberInStock = $number;
		return $this;
	}
	
	/**
	 * @param integer $number The number to add
	 *
	 * @param \CudiBundle\Entity\Stock\StockItem
	 */
	public function addNumber($number)
	{
		$this->numberInStock += $number;
		if ($this->numberInStock < 0)
			$this->numberInStock = 0;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getTotalOrdered()
	{
		$period = $this->_entityManager
			->getRepository('CudiBundle\Entity\Stock\Period')
			->findOpen();
		
		return $this->_entityManager
		    ->getRepository('CudiBundle\Entity\Stock\Period')
		    ->getNbOrderedByArticle($period, $this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberNotDelivered()
	{
		return $this->getTotalOrdered() - $this->getTotalDelivered();
	}
	
	/**
	 * @return integer
	 */
	public function getNumberQueueOrder()
	{
		$item = $this->_entityManager
			->getRepository('CudiBundle\Entity\Stock\OrderItem')
			->findOneOpenByArticle($this->article);
		
		if (null === $item)
			return 0;
		
		return $item->getNumber();
	}
	
	/**
	 * @return integer
	 */
	public function getTotalDelivered()
	{
	    $period = $this->_entityManager
	    	->getRepository('CudiBundle\Entity\Stock\Period')
	    	->findOpen();
	    
	    return $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Stock\Period')
	        ->getNbDeliveredByArticle($period, $this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberBooked()
	{
    	$period = $this->_entityManager
    		->getRepository('CudiBundle\Entity\Stock\Period')
    		->findOpen();
    	
    	return $this->_entityManager
    	    ->getRepository('CudiBundle\Entity\Stock\Period')
    	    ->getNbBookedByArticle($period, $this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberAssigned()
	{
	    $period = $this->_entityManager
	    	->getRepository('CudiBundle\Entity\Stock\Period')
	    	->findOpen();
	    
	    return $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Stock\Period')
	        ->getNbAssignedByArticle($period, $this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberSold()
	{
		$period = $this->_entityManager
			->getRepository('CudiBundle\Entity\Stock\Period')
			->findOpen();
		
		return $this->_entityManager
		    ->getRepository('CudiBundle\Entity\Stock\Period')
		    ->getNbSoldByArticle($period, $this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberAvailable()
	{
		return $this->numberInStock - $this->getNumberAssigned();
	}
	
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 *
	 * @return \CudiBundle\Entity\Stock\StockItem
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		$this->_entityManager = $entityManager;
		return $this;
	}
}