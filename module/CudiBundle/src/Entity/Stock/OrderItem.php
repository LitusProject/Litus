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
	CudiBundle\Entity\Stock\Order;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\OrderItem")
 * @Table(name="cudi.stock_orderitem")
 */
class OrderItem
{
	/**
	 * @var integer The ID of the order item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of the order item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CudiBundle\Entity\Stock\Order The order of the order item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Stock\Order", inversedBy="orderItems")
	 * @JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $order;
	
	/**
	 * @var integer The number of items ordered
	 *
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * Create a new order item.
	 *
	 * @param \CudiBundle\Entity\Article $article The stock Item
	 * @param \CudiBundle\Entity\Stock\Order $order The order
	 * @param integer $number The number of items
	 */
	public function __construct(Article $article, Order $order, $number)
	{
		$this->setArticle($article);
		$this->order = $order;
		$this->setNumber($number);
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\Order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return \CudiBundle\Entity\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @param \CudiBundle\Entity\Article $article The new article of this order
	 * 
	 * @return \CudiBundle\Entity\Stock\OrderItem
	 */
	public function setArticle(Article $article)
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
	 * @return \CudiBundle\Entity\Stock\OrderItem
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
