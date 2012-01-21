<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\DeliveryItem")
 * @Table(name="cudi.stock_deliveryitem")
 */
class DeliveryItem
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
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * @param \Litus\Entity\Cudi\Article $article The article
	 * @param integer $number The number of this article
	 */
	public function __construct($article, $number)
	{
		if (null === $article->getStockItem())
			throw new \InvalidArgumentException('The article is not valid.');
			
		$this->article = $article;
		$this->date = new \DateTime();
		$this->number = $number;
		$article->getStockItem()->addNumber($number);
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->article->getPurchasePrice() * $this->number;
	}
}
