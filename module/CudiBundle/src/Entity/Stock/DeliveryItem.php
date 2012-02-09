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

use CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\DeliveryItem")
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
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
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
	 * @param CudiBundle\Entity\Article $article The article
	 * @param integer $number The number of this article
	 */
	public function __construct(Article $article, $number)
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
	 * @return CudiBundle\Entity\Article
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
