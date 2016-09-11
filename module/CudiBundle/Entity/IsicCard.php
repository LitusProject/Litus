<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 13/08/16
 * Time: 14:50
 */

namespace CudiBundle\Entity;


use CommonBundle\Entity\General\AcademicYear,
    DateTime;

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
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var The academic year the card is valid
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var The date the card was ordered
     *
     * @ORM\Column(name="orderdate", type="datetime", nullable=false)
     */
    private $orderDate;

    /**
     * @var Is the card delivered
     *
     * @ORM\Column(type="boolean")
     */
    private $delivered;

    /**
     * @var Did the person collect his card
     *
     * @ORM\Column(type="boolean")
     */
    private $retrieved;

    public function __construct(Person $person, AcademicYear $academicYear)
    {
        $this->person = $person;
        $this->academicYear = $academicYear;
        $this->orderDate = new DateTime();
        $this->delivered = false;
        $this->retrieved = false;

    }

    public function getId()
    {
        return $this->id;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function getDelivered()
    {
        return $this->delivered;
    }

    public function getRetrieved()
    {
        return $this->retrieved;
    }

    public function setDelivered($delivered)
    {
        $this->delivered = $delivered;
    }

    public function setRetrieved($retrieved)
    {
        $this->retrieved = $retrieved;
    }
}