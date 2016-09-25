<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 13/08/16
 * Time: 14:50
 */

namespace CudiBundle\Entity;


use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Booking,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\IsicCard")
 * @ORM\Table(name="cudi.isic_card")
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
     * @var The owner of the card
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var integer The ID of the card
     *
     * @ORM\Column(type="string")
     */
    private $cardNumber;

    /**
     * @var The academic year the card is valid
     *
     * @ORM\OneToOne(targetEntity="CudiBundle\Entity\Sale\Booking")
     * @ORM\JoinColumn(name="booking", referencedColumnName="id")
     */
    private $booking;

    /**
     * @var The academic year the card is valid
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    public function __construct(Person $person, $cardNumber, Booking $booking, AcademicYear $academicYear)
    {
        $this->person = $person;
        $this->cardNumber = $cardNumber;
        $this->booking = $booking;
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

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function setAcademicYear()
    {
        $this->academicYear = $academicYear;
    }
}