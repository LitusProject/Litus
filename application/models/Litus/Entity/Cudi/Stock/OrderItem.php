<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\OrderItem")
 * @Table(name="cudi.stock_orderitem")
 */
class OrderItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\Order", inversedBy="orderItems")
	 * @JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $order;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * Create a new order item.
	 *
	 * @param \Litus\Entity\Cudi\Article $article The stock Item
	 * @param \Litus\Entity\Cudi\Stock\Order $order The order
	 * @param integer $number The number of items
	 */
	public function __construct($article, $order, $number)
	{
		$this->article = $article;
		$this->order = $order;
		$this->number = $number;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Stock\Order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @param \Litus\Entity\Cudi\Article $article The new article of this order
	 * 
	 * @return \Litus\Entity\Cudi\Stock\OrderItem
	 */
	public function setArticle($article)
	{
		$this->article = $article;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @param integer $number The number of items.
	 *
	 * @return \Litus\Entity\Cudi\Stock\OrderItem
	 */
	public function setNumber($number)
	{
		$this->number = $number;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->article->getPurchasePrice() * $this->number;
	}
}
