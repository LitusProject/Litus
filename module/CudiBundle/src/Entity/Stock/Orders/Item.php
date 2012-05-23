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
 
namespace CudiBundle\Entity\Stock\Orders;

use CudiBundle\Entity\Sales\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\Orders\Item")
 * @Table(name="cudi.stock_orders_item")
 */
class Item
{
	/**
	 * @var integer The ID of the item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Article The article of the item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CudiBundle\Entity\Stock\Orders\Order The order of the item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Stock\Orders\Order", inversedBy="items")
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
	 * @param \CudiBundle\Entity\Sales\Article $article The article of the item
	 * @param \CudiBundle\Entity\Stock\Orders\Order $order The order of the item
	 * @param integer $number The number of items ordered
	 */
	public function __construct(Article $article, Order $order, $number)
	{
		$this->article = $article;
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
	 * @return \CudiBundle\Entity\Stock\Orders\Order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @param integer $number
	 *
	 * @return \CudiBundle\Entity\Stock\Orders\Item
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
