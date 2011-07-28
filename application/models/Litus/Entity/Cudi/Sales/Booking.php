<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\Booking")
 * @Table(name="cudi.sales_booking")
 */
class Booking
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockArticle;
	
	/**
	 * @ManyToOne(targetEntity="Litus\Entity\Cudi\Sales\BookingStatus", cascade={"ALL"}, fetch="LAZY")
	 */
	private $status;
	
	/**
	 * @Column(type="datetime")
	 */
	private $expirationDate;
	
	/**
	 * @Column(type="datetime")
	 */
	private $assignmentDate;
	
	/**
	 * @Column(type="datetime")
	 */
	private $bookDate;
	
	/**
	 * @Column(type="datetime")
	 */
	private $saleDate;
	
	/**
	 * @Column(type="datetime")
	 */
	private $cancelationDate;
	
	public function canHaveBookingStatus()
    {
        return true;
    }
}
