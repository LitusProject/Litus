<?php
 
namespace Litus\Entity\Cudi\Stock;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;
 
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
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article", inversedBy="stockItem")
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
	 * @return \Litus\Entity\Cudi\Article
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
	}
	
	/**
	 * @return integer
	 */
	public function getTotalOrdered()
	{
		$total = Registry::get(DoctrineResource::REGISTRY_KEY)->getRepository('Litus\Entity\Cudi\Stock\Order')->getTotalOrdered($this->article);
		
		return $total;
	}
	
	/**
	 * @return integer
	 */
	public function getNumberOpenOrder()
	{
		$item = Registry::get(DoctrineResource::REGISTRY_KEY)->getRepository('Litus\Entity\Cudi\Stock\OrderItem')->findOneOpenByArticle($this->article);
		
		if (null === $item)
			return 0;
		
		return $item->getNumber();
	}
	
	/**
	 * @return integer
	 */
	public function getTotalDelivered()
	{
		$total = Registry::get(DoctrineResource::REGISTRY_KEY)->getRepository('Litus\Entity\Cudi\Stock\DeliveryItem')->getTotalByArticle($this->article);
		
		return $total;
	}
	
	/**
	 * @return integer
	 */
	public function getNumberBooked()
	{
		return 0;
	}
}