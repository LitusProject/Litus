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

use CudiBundle\Entity\Sales\Booking,
    CudiBundle\Entity\Sales\ServingQueueItem,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\SaleItem")
 * @Table(name="cudi.sales_saleitem")
 */
class SaleItem
{
	/**
	 * @var integer The ID of this sale item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Session The session of this sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
	 * @JoinColumn(name="session_id", referencedColumnName="id")
	 */
	private $session;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of this sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
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
	 * @var \CudiBundle\Entity\Sales\Booking The booking belonging to this sale item
	 *
	 * @OneToOne(targetEntity="CudiBundle\Entity\Sales\Booking")
	 * @JoinColumn(name="booking", referencedColumnName="id")
	 */
	private $booking;
	
	/**
	 * @var \CudiBundle\Entity\Sales\ServingQueueItem The queue item belonging to this sale item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\ServingQueueItem")
	 * @JoinColumn(name="serving_queue_item", referencedColumnName="id")
	 */
	private $servingQueueItem;
	
	/**
	 * @param integer $price
	 * @param \CudiBundle\Entity\Sales\Booking $booking
	 * @param \CudiBundle\Entity\Sales\ServingQueueItem $servingQueueItem
	 */
	public function __construct($price, Booking $booking, ServingQueueItem $servingQueueItem)
	{
	    $this->session = $servingQueueItem->getSession();
	    $this->article = $booking->getArticle();
	    $this->number = $booking->getNumber();
	    $this->price = $price * 100;
	    $this->booking = $booking;
	    $this->servingQueueItem = $servingQueueItem;
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
	 * @return \CudiBundle\Entity\Sales\SaleItem
	 */
	public function setNumber($number)
	{
		$this->number = $number;
		return $this;
	}
}
