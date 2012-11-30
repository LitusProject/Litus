<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Cv;

use Doctrine\Common\Collections\ArrayCollection,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Address,
    SyllabusBundle\Entity\Study,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a cv entry.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Cv\Entry")
 * @ORM\Table(name="br.cv_entries")
 */
class Entry
{
    /**
     * @var string The entry's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The academic to whom this cv belongs
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year in which this cv was created.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist"})
     * @ORM\JoinColumn(name="year", referencedColumnName="id")
     */
    private $year;

    /**
     * @var string The first name.
     *
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string The last name.
     *
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var \DateTime The user's birthday
     *
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @var string The persons sex ('m' or 'f')
     *
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $sex;

    /**
     * @var string The persons telephone number
     *
     * @ORM\Column(type="string", length=15)
     */
    private $phoneNumber;

    /**
     * @var string The user's personal email
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the cv entry
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var \DateTime The study of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(name="study", referencedColumnName="id")
     */
    private $study;

    /**
     * @var int The user's personal email
     *
     * @ORM\Column(name="bachelor_start", type="integer")
     */
    private $bachelorStart;

    /**
     * @var int The user's personal email
     *
     * @ORM\Column(name="bachelor_end", type="integer")
     */
    private $bachelorEnd;

    /**
     * @var int The user's personal email
     *
     * @ORM\Column(name="master_start", type="integer")
     */
    private $masterStart;

    /**
     * @var int The user's personal email
     *
     * @ORM\Column(name="master_end", type="integer")
     */
    private $masterEnd;

    /**
     * @var string Additional diplomas.
     *
     * @ORM\Column(name="additional_diplomas", type="text")
     */
    private $additionalDiplomas;

    /**
     * @var string Erasmus period.
     *
     * @ORM\Column(name="erasmus_period", type="string")
     */
    private $erasmusPeriod;

    /**
     * @var string Erasmus location.
     *
     * @ORM\Column(name="erasmus_location", type="string")
     */
    private $erasmusLocation;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The languages added to this cv
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Cv\Language", mappedBy="entry", cascade={"persist", "remove"})
     */
    private $languages;

    /**
     * @var string Computer skills.
     *
     * @ORM\Column(name="computer_skills", type="text")
     */
    private $computerSkills;

    /**
     * @var string Experiences.
     *
     * @ORM\Column(type="text")
     */
    private $experiences;

    /**
     * @var string Thesis summary.
     *
     * @ORM\Column(name="thesis_summary", type="text")
     */
    private $thesisSummary;

    /**
     * @var string Field of interest.
     *
     * @ORM\Column(name="future_interest", type="string")
     */
    private $futureInterest;

    /**
     * @var string Mobility inside europe.
     *
     * @ORM\Column(name="mobility_europe", type="string")
     */
    private $mobilityEurope;

    /**
     * @var string Mobility in the world.
     *
     * @ORM\Column(name="mobility_world", type="string")
     */
    private $mobilityWorld;

    /**
     * @var string Career expectations.
     *
     * @ORM\Column(name="career_expectations", type="text")
     */
    private $careerExpectations;

    /**
     * @var string Hobbies.
     *
     * @ORM\Column(name="hobbies", type="text")
     */
    private $hobbies;

    /**
     * @var string About me.
     *
     * @ORM\Column(name="about", type="text")
     */
    private $about;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $academic The academic
     * @param \CommonBundle\Entity\General\AcademicYear $year The current academic year.
     */
    public function __construct(Academic $academic, AcademicYear $year, $firstName, $lastName, $birthday,
        $sex, $phoneNumber, $email, Address $address, Study $study, $bachelorStart, $bachelorEnd, $masterStart, $masterEnd,
        $additionalDiplomas, $erasmusPeriod, $erasmusLocation, $computerSkills, $experiences,
        $thesisSummary, $futureInterest, $mobilityEurope, $mobilityWorld, $careerExpectations, $hobbies, $about)
    {
        $this->academic = $academic;
        $this->year = $year;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthday = $birthday;
        $this->sex = $sex;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->address = $address;
        $this->study = $study;
        $this->bachelorStart = $bachelorStart;
        $this->bachelorEnd = $bachelorEnd;
        $this->masterStart = $masterStart;
        $this->masterEnd = $masterEnd;
        $this->additionalDiplomas = $additionalDiplomas;
        $this->erasmusPeriod = $erasmusPeriod;
        $this->erasmusLocation = $erasmusLocation;
        $this->computerSkills = $computerSkills;
        $this->experiences = $experiences;
        $this->thesisSummary = $thesisSummary;
        $this->futureInterest = $futureInterest;
        $this->mobilityEurope = $mobilityEurope;
        $this->mobilityWorld = $mobilityWorld;
        $this->careerExpectations = $careerExpectations;
        $this->hobbies = $hobbies;
        $this->about = $about;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getSex()
    {
        return $this->sex;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getStudy()
    {
        return $this->study;
    }

    public function getBachelorStart()
    {
        return $this->bachelorStart;
    }

    public function getBachelorEnd()
    {
        return $this->bachelorEnd;
    }

    public function getMasterStart()
    {
        return $this->masterStart;
    }

    public function getMasterEnd()
    {
        return $this->masterEnd;
    }

    public function getAdditionalDiplomas()
    {
        return $this->additionalDiplomas;
    }

    public function getErasmusPeriod()
    {
        return $this->erasmusPeriod;
    }

    public function getErasmusLocation()
    {
        return $this->erasmusLocation;
    }

    public function getLanguages()
    {
        return $this->languages->toArray();
    }

    public function getComputerSkills()
    {
        return $this->computerSkills;
    }

    public function getExperiences()
    {
        return $this->experiences;
    }

    public function getThesisSummary()
    {
        return $this->thesisSummary;
    }

    public function getFutureInterest()
    {
        return $this->futureInterest;
    }

    public function getMobilityEurope()
    {
        return $this->mobilityEurope;
    }

    public function getMobilityWorld()
    {
        return $this->mobilityWorld;
    }

    public function getCareerExpectations()
    {
        return $this->careerExpectations;
    }

    public function getHobbies()
    {
        return $this->hobbies;
    }

    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @return The id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getYear()
    {
        return $this->year;
    }

}
