<?php

namespace Litus\Entities\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Sales\BookingRepository")
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
	 * @ManyToOne(targetEntity="\Litus\Entities\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockArticle;
	
	/**
	 * @ManyToOne(targetEntity="Litus\Entities\Cudi\Sales\BookingStatus", cascade={"ALL"}, fetch="LAZY")
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
