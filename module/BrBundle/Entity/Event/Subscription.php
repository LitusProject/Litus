<?php

namespace BrBundle\Entity\Event;

use BrBundle\Entity\Company;
use BrBundle\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\Subscription")
 * @ORM\Table(name="br_events_subscriptions", uniqueConstraints={@ORM\UniqueConstraint(name="event_qr",columns={"event_id", "qr_code"})})
 */
class Subscription
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *@var Event The event that the company will be attending
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

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
     * @var string University of the subscriber
     *
     * @ORM\Column(name="university", type="text")
     *
     */
    private $university;


    const POSSIBLE_UNIVERSITIES = array(
        'ku leuven'  => 'KU Leuven',
        'vub'        => 'Vrije Universiteit Brussel',
        'ugent'      => 'UGent',
        'uhasselt'   => 'UHasselt',
        'uantwerpen' => 'UAntwerpen',
        'other'      => 'Other',
    );



    /**
     * @var string University of the subscriber if it is not one of the available
     *
     * @ORM\Column(name="other_university", type="text", nullable=true)
     *
     */
    private $otherUniversity;

    /**
     * @var string Study of the subscriber
     *
     * @ORM\Column(name="study", type="text")
     *
     */
    private $study;

    /**
     * @var string Study of the subscriber if it is not one of the available
     *
     * @ORM\Column(name="other_study", type="text", nullable=true)
     *
     */
    private $otherStudy;


    const POSSIBLE_STUDIES = Company::POSSIBLE_MASTERS + array(
        'faculty of bio engineering'        => 'Faculty of Bio Engineering',
        'faculty of business and economics' => 'Faculty of Business and Economics',
        'faculty of engineering technology' => 'Faculty of Engineering Technology',
        'other'                             => 'other', //changed to lowercase to avoid translation
    );

    /**
     * @var string Specialization of the subscriber
     *
     * @ORM\Column(name="specialization", type="text", nullable=true)
     *
     */
    private $specialization;

    /**
     * @var string study year of the subscriber
     *
     * @ORM\Column(name="study_year", type="text")
     *
     */
    private $studyYear;


    const POSSIBLE_STUDY_YEARS = array(
        'bach1'  => '1st Bachelor',
        'bach2'  => '2nd Bachelor',
        'bach3'  => '3rd Bachelor',
        'ma1'    => '1st Master',
        'ma2'    => '2nd Master',
        'manama' => 'ManaMa',
        'phd'    => 'PhD',
        'other'  => 'Other',
    );

    /**
     * @var string Food of the subscriber
     *
     * @ORM\Column(name="food", type="text", nullable=true)
     *
     */
    private $food;

    /**
     * @var string Unique identifier for QR code of the subscriber
     *
     * @ORM\Column(name="qr_code", type="text", unique=true)
     *
     */
    private $qrCode;

    /**
     * @var boolean Subscriber will be at the network reception
     *
     * @ORM\Column(name="network_reception", type="boolean", nullable=true)
     *
     */
    private $atNetworkReception;

    /**
     * @var boolean Subscriber gave consent to pass his data for QR codes
     *
     * @ORM\Column(name="consent", type="boolean")
     *
     */
    private $consent;

    /**
     * Subscription constructor. It generates a random 20 hexadecimal characters qr code
     */
    public function __construct()
    {
        try {
            $this->qrCode = bin2hex(random_bytes(10));
        } catch (\Exception $e) {
            echo 'Something went wrong';
        }
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber ?? '';
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getUniversity(): string
    {
        return $this->university;
    }

    /**
     * @return string
     */
    public function getOtherUniversity(): string
    {
        return $this->otherUniversity ?? '';
    }

    /**
     * @return string
     */
    public function getUniversityString(): string
    {
        if ($this->university == 'other') {
            return ($this->otherUniversity ?? ' ');
        }
        return $this::POSSIBLE_UNIVERSITIES[$this->university];
    }

    /**
     * @param string $university
     */
    public function setUniversity(string $university): void
    {
        $this->university = $university;
    }

    /**
     * @param string $university
     */
    public function setOtherUniversity($university): void
    {
        $this->otherUniversity = $university;
    }

    /**
     * @return string
     */
    public function getStudy(): string
    {
        return $this->study;
    }

    /**
     * @return string
     */
    public function getOtherStudy(): string
    {
        return $this->otherStudy ?? '';
    }

    /**
     * @return string
     */
    public function getStudyString(): string
    {
        if ($this->study == 'other') {
            return ($this->otherStudy ?? ' ');
        }
        return $this::POSSIBLE_STUDIES[$this->study];
    }

    /**
     * @param string $study
     */
    public function setStudy(string $study): void
    {
        $this->study = $study;
    }

    /**
     * @param string $study
     */
    public function setOtherStudy($study): void
    {
        $this->otherStudy = $study;
    }

    /**
     * @return string
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * @param string $specialization
     */
    public function setSpecialization(string $specialization): void
    {
        $this->specialization = $specialization;
    }

    /**
     * @return string
     */
    public function getStudyYear(): string
    {
        return $this->studyYear;
    }

    /**
     * @return string
     */
    public function getStudyYearString(): string
    {
        return self::POSSIBLE_STUDY_YEARS[$this->getStudyYear()];
    }

    /**
     * @param string $studyYear
     */
    public function setStudyYear(string $studyYear): void
    {
        $this->studyYear = $studyYear;
    }

    /**
     * @return string
     */
    public function getFood(): string
    {
        return ($this->food ?? '');
    }

    /**
     * @param string $food
     */
    public function setFood(string $food): void
    {
        $this->food = $food;
    }

    /**
     * @return string
     */
    public function getQrCode(): string
    {
        return $this->qrCode;
    }

    /**
     * @return string
     */
    public function getFoodString(): string
    {
        return ($this->food ? $this->event->getFood()[$this->food] : '');
    }

    /**
     * @param string $qrCode
     */
    public function setQrCode(string $qrCode): void
    {
        $this->qrCode = $qrCode;
    }

    /**
     * @return boolean
     */
    public function isAtNetworkReception(): bool
    {
        return $this->atNetworkReception ?? false;
    }

    /**
     * @param boolean $atNetworkReception
     */
    public function setAtNetworkReception(bool $atNetworkReception): void
    {
        $this->atNetworkReception = $atNetworkReception;
    }

    /**
     * @return boolean
     */
    public function gaveConsent(): bool
    {
        return $this->consent;
    }

    /**
     * @param boolean $consent
     */
    public function setConsent(bool $consent): void
    {
        $this->consent = $consent;
    }
}
