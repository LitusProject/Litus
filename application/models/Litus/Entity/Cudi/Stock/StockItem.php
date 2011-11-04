<?php
 
namespace Litus\Entity\Cudi\Stock;
 
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
	
	public function __construct($article)
	{
		$this->article = $article;
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
		return 0;
	}
}