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
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
	 * @JoinColumn(name="session_id", referencedColumnName="id")
	 */
	private $session;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * @Column(type="datetime")
	 */
	private $timestamp;
	
	/**
	 * @Column(type="integer")
	 */
	private $price;
	
	/**
	 * @OneToOne(targetEntity="CudiBundle\Entity\Sales\Booking")
	 * @JoinColumn(name="booking", referencedColumnName="id")
	 */
	private $booking;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\ServingQueueItem")
	 * @JoinColumn(name="serving_queue_item", referencedColumnName="id")
	 */
	private $servingQueueItem;
	
	public function __construct($price, Booking $booking, ServingQueueItem $servingQueueItem)
	{
	    $this->session = $servingQueueItem->getSession();
	    $this->article = $booking->getArticle();
	    $this->number = $booking->getNumber();
	    $this->timestamp = new DateTime();
	    $this->price = $price * 100;
	    $this->booking = $booking;
	    $this->servingQueueItem = $servingQueueItem;
	}
}
