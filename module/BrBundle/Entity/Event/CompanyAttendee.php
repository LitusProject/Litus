<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\CompanyAttendee")
 * @ORM\Table(name="br_events_companies_attendee")
 */
class CompanyAttendee
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CompanyMap The company that will be attending this event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event\CompanyMap", inversedBy="attendees")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $companyMap;

    /**
     * @var string first name of the subscriber
     *
     * @ORM\Column(name="first_name", type="text")
     *
     */
    private $firstName;

    /**
     * @var string last name of the subscriber
     *
     * @ORM\Column(name="last_name", type="text")
     *
     */
    private $lastName;

    /**
     * @var string email address of the subscriber
     *
     * @ORM\Column(name="email", type="text")
     *
     */
    private $email;

    /**
     * @var string phone number of the subscriber
     *
     * @ORM\Column(name="phone_number", type="text", nullable=true)
     *
     */
    private $phoneNumber;

    /**
     * @var boolean whether ot not the attendee wants lunch
     *
     * @ORM\Column(name="lunch", type="boolean")
     *
     */
    private $lunch;

    /**
     * @var boolean whether ot not the attendee is a vegetarian
     *
     * @ORM\Column(name="veggie", type="boolean")
     *
     */
    private $veggie;

    /**
     * CompanyAttendee constructor.
     * @param int $id
     * @param \BrBundle\Entity\Event\CompanyMap $companyMap
     */
    public function __construct(\BrBundle\Entity\Event\CompanyMap $companyMap)
    {
        $this->companyMap = $companyMap;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Event\CompanyMap
     */
    public function getCompanyMap()
    {
        return $this->companyMap;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return bool
     */
    public function isLunch()
    {
        return $this->lunch;
    }

    /**
     * @param bool $lunch
     */
    public function setLunch($lunch)
    {
        $this->lunch = $lunch;
    }

    /**
     * @return bool
     */
    public function isVeggie()
    {
        return $this->veggie;
    }

    /**
     * @param bool $veggie
     */
    public function setVeggie($veggie)
    {
        $this->veggie = $veggie;
    }



}



