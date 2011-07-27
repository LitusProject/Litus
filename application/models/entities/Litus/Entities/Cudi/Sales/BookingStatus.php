<?php

namespace Litus\Entities\Cudi\Sales;

use Litus\Entities\Cudi\Sales\Booking;
use \InvalidArgumentException;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Sales\BookingStatusRepository")
 * @Table(name="cudi.sales_bookingstatus")
 */
class BookingStatus
{
    /**
     * All the possible status values allowed.
     *
     * @var array
     */
    private static $POSSIBLE_STATUSES = array('booked', 'assigned', 'sold', 'expired');

	/**
     * The ID of this BookingStatus.
     *
     * @var int
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * The Booking this BookingStatus describes.
     *
     * @var Booking
     *
     * @Column(name="booking")
     * @ManyToOne(targetEntity="Litus\Entities\Cudi\Sales\Booking", cascade={"all"}, fetch="LAZY")
     */
    private $booking;

    /**
     * The actual status value.
     *
     * @var string
     *
     * @Column(type="string")
     */
    private $status;

    public function __construct(Booking $booking, $status)
    {
        if(BookingStatus::isValidBooking($booking))
            $this->booking = $booking;
        else
            throw new InvalidArgumentException('Invalid booking: ' . $booking);
        $this->setStatus($status);
    }

    /**
     * Returns the unique ID of this BookingStatus.
     *
     * @return int the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the booking.
     *
     * @return Booking the booking this BookingStatus belongs to.
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Returns whether the given person can have a BookingStatus.
     *
     * @static
     * @param Booking $booking the booking to check
     * @return bool
     */
    public static function isValidPerson(Booking $booking)
    {
        return ($booking != null) && $Booking->canHaveBookingStatus();
    }

    /**
     * Returns the actual value.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status to the given value if valid.
     *
     * @see isValidStatus($status)
     * @param $status string the status to set
     * @return void doesn't return anything
     */
    public function setStatus($status)
    {
        if($this->isValidStatus($status))
            $this->status = $status;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param $status string a status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return (array_search($status, BookingStatus::$POSSIBLE_STATUSES, true) != false);
    }
}
