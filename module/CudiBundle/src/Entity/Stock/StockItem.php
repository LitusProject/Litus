<?php
 
namespace CudiBundle\Entity\Stock;

use Doctrine\ORM\EntityManager;
 
/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\StockItem")
 * @Table(name="cudi.stock_stockitem")
 */
class StockItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article", inversedBy="stockItem")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @Column(type="integer")
	 */
	private $numberInStock;
	
	public function __construct($article)
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
	 * @return CudiBundle\Entity\Article
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
	 */
	public function setNumberInStock($number)
	{
		$this->numberInStock = $number;
	}
	
	/**
	 * @param integer $number The number to add
	 */
	public function addNumber($number)
	{
		$this->numberInStock += $number;
		if ($this->numberInStock < 0)
			$this->numberInStock = 0;
	}
	
	/**
	 * @return integer
	 */
	public function getTotalOrdered(EntityManager $entityManager)
	{
		$total = $entityManager
			->getRepository('CudiBundle\Entity\Stock\Order')
			->getTotalOrdered($this->article);
		
		return $total;
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
	public function getNumberQueueOrder(EntityManager $entityManager)
	{
		$item = $entityManager
			->getRepository('CudiBundle\Entity\Stock\OrderItem')
			->findOneOpenByArticle($this->article);
		
		if (null === $item)
			return 0;
		
		return $item->getNumber();
	}
	
	/**
	 * @return integer
	 */
	public function getTotalDelivered(EntityManager $entityManager)
	{
		return $entityManager
			->getRepository('CudiBundle\Entity\Stock\DeliveryItem')
			->getTotalByArticle($this->article);
	}
	
	/**
	 * @return integer
	 */
	public function getNumberBooked(EntityManager $entityManager)
	{
		$booked = $entityManager
			->getRepository('CudiBundle\Entity\Sales\Booking')
			->findAllBookedByArticle($this->article);
		
		$number = 0;
		foreach($booked as $booking)
			$number += $booking->getNumber();
		
		return $number;
	}
	
	/**
	 * @return integer
	 */
	public function getNumberAssigned(EntityManager $entityManager)
	{
		$booked = $entityManager
			->getRepository('CudiBundle\Entity\Sales\Booking')
			->findAllAssignedByArticle($this->article);
		
		$number = 0;
		foreach($booked as $booking)
			$number += $booking->getNumber();
		
		return $number;
	}
	
	/**
	 * @return integer
	 */
	public function getNumberSold(EntityManager $entityManager)
	{
		$booked = $entityManager
			->getRepository('CudiBundle\Entity\Sales\Booking')
			->findAllSoldByArticle($this->article);
		
		$number = 0;
		foreach($booked as $booking)
			$number += $booking->getNumber();
		
		return $number;
	}
	
	/**
	 * @return integer
	 */
	public function getNumberAvailable(EntityManager $entityManager)
	{
		return $this->numberInStock - $this->getNumberBooked($entityManager);
	}
}