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
 
namespace CudiBundle\Entity\Sales;

use CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Sales\QueueItem,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\SaleItem")
 * @Table(name="cudi.sales_saleitem", indexes={@index(name="sales_saleitem_time", columns={"timestamp"})})
 */
class SaleItem
{
	/**
	 * @var integer The ID of the sale item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \DateTime The time the sale item was created
	 *
	 * @Index
	 * @Column(type="datetime")
	 */
	private $timestamp;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Session The session of the sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
	 * @JoinColumn(name="session", referencedColumnName="id")
	 */
	private $session;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Article The article of the sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var integer The number sold of the article
	 *
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * @var integer The price of the selling
	 *
	 * @Column(type="integer")
	 */
	private $price;
	
	/**
	 * @var \CudiBundle\Entity\Sales\QueueItem The queue item belonging to the sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\QueueItem")
	 * @JoinColumn(name="queue_item", referencedColumnName="id")
	 */
	private $queueItem;
	
	/**
	 * @param \CudiBundle\Entity\Sales\Article $article
	 * @param integer $number
	 * @param integer $price
	 * @param \CudiBundle\Entity\Sales\QueueItem $queueItem
	 */
	public function __construct(Article $article, $number, $price, QueueItem $queueItem)
	{
	    $this->session = $queueItem->getSession();
	    $this->article = $article;
	    $this->number = $number;
	    $this->price = $price * 100;
	    $this->queueItem = $queueItem;
	    $this->timestamp = new DateTime();
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
	    return $this->id;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getTimestamp()
	{
	    return $this->timestamp;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\Session
	 */
	public function getSession()
	{
	    return $this->session;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\Article
	 */
	public function getArticle()
	{
	    return $this->article;
	}
	
	/**
	 * @param integer $number
	 *
	 * @return \CudiBundle\Entity\Sales\SaleItem
	 */
	public function setNumber($number)
	{
	    $this->price = round($this->price * $number / $this->number);
		$this->number = $number;
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
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\QueueItem
	 */
	public function getQueueItem()
	{
		return $this->queueItem;
	}
	
	/**
	 * @return \CommonBundle\Entity\Users\person
	 */
	public function getPerson()
	{
		return $this->queueItem->getPerson();
	}
}
