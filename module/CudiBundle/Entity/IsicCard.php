<?php

namespace CudiBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Booking;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\IsicCard")
 * @ORM\Table(
 *     name="cudi_isic_cards",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="cudi_isic_cards_person_academic_year", columns={"person", "academic_year"})}
 * )
 */
class IsicCard
{
    /**
     * @var integer The ID of the card
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The owner of the card
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var integer The ID of the card or the user (delayed orders get a userID, immediate orders a card number)
     *
     * @ORM\Column(type="string")
     */
    private $cardNumber;

    /**
     * @var boolean If a card number has already been paid
     *
     * @ORM\Column(type="boolean",options={"default":false})
     */
    private $hasPaid;

    /**
     * @var \CudiBundle\Entity\Sale\Booking The academic year the card is valid
     *
     * @ORM\OneToOne(targetEntity="CudiBundle\Entity\Sale\Booking")
     * @ORM\JoinColumn(name="booking", referencedColumnName="id")
     */
    private $booking;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The academic year the card is valid
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    public function __construct(Person $person, $cardNumber, Booking $booking, $hasPaid, AcademicYear $academicYear)
    {
        $this->person = $person;
        $this->cardNumber = $cardNumber;
        $this->booking = $booking;
        $this->hasPaid = $hasPaid;
        $this->academicYear = $academicYear;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function getBooking()
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function hasPaid()
    {
        return $this->hasPaid;
    }

    public function setPaid($hasPaid)
    {
        $this->hasPaid = $hasPaid;
    }

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function setAcademicYear($academicYear)
    {
        $this->academicYear = $academicYear;
    }
}
