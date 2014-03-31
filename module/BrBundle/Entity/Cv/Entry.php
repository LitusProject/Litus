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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Cv;

use Doctrine\Common\Collections\ArrayCollection,
    CommonBundle\Entity\User\Person\Academic,
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
     * @var \CommonBundle\Entity\User\Person\Academic The academic to whom this cv belongs
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
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
     * @var string The prior study
     *
     * @ORM\Column(name="prior_study", type="string")
     */
    private $priorStudy;

    /**
     * @var int The prior grade
     *
     * @ORM\Column(name="prior_grade", type="bigint")
     */
    private $priorGrade;

    /**
     * @var \DateTime The study of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(name="study", referencedColumnName="id")
     */
    private $study;

    /**
     * @var int The grade of the current study.
     *
     * @ORM\Column(name="grade", type="bigint")
     */
    private $grade;

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
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $languages;

    /**
     * @var string Extra information regarding language knowledge.
     *
     * @ORM\Column(name="language_extra", type="text")
     */
    private $languageExtra;

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
     * @param \CommonBundle\Entity\User\Person\Academic $academic           The academic
     * @param \CommonBundle\Entity\General\AcademicYear $year               The current academic year.
     * @param integer                                   $priorStudy
     * @param integer                                   $priorGrade
     * @param integer                                   $grade
     * @param integer                                   $bachelorStart
     * @param integer                                   $bachelorEnd
     * @param integer                                   $masterStart
     * @param integer                                   $masterEnd
     * @param integer                                   $additionalDiplomas
     * @param integer                                   $erasmusPeriod
     * @param integer                                   $erasmusLocation
     * @param integer                                   $languageExtra
     * @param integer                                   $computerSkills
     * @param integer                                   $experiences
     * @param integer                                   $thesisSummary
     * @param integer                                   $futureInterest
     * @param integer                                   $mobilityEurope
     * @param integer                                   $mobilityWorld
     * @param integer                                   $careerExpectations
     * @param integer                                   $hobbies
     * @param integer                                   $about
     */
    public function __construct(Academic $academic, AcademicYear $year, $firstName, $lastName, $birthday,
        $sex, $phoneNumber, $email, Address $address, $priorStudy, $priorGrade, Study $study, $grade, $bachelorStart,
        $bachelorEnd, $masterStart, $masterEnd, $additionalDiplomas, $erasmusPeriod, $erasmusLocation, $languageExtra,
        $computerSkills, $experiences, $thesisSummary, $futureInterest, $mobilityEurope, $mobilityWorld,
        $careerExpectations, $hobbies, $about)
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
        $this->priorStudy = $priorStudy;
        $this->priorGrade = $priorGrade * 100;
        $this->study = $study;
        $this->grade = $grade * 100;
        $this->bachelorStart = $bachelorStart;
        $this->bachelorEnd = $bachelorEnd;
        $this->masterStart = $masterStart;
        $this->masterEnd = $masterEnd;
        $this->additionalDiplomas = $additionalDiplomas;
        $this->erasmusPeriod = $erasmusPeriod;
        $this->erasmusLocation = $erasmusLocation;
        $this->languageExtra = $languageExtra;
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

    /**
     * Retrieves the firstName of this entry.
     *
     * @return firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Changes the firstName of this cv entry to the given value.
     *
     * @param firstName The new value
     * @return Entry this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Retrieves the lastName of this entry.
     *
     * @return lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Changes the lastName of this cv entry to the given value.
     *
     * @param lastName The new value
     * @return Entry this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Retrieves the birthday of this entry.
     *
     * @return birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Changes the birthday of this cv entry to the given value.
     *
     * @param birthday The new value
     * @return Entry this
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Retrieves the sex of this entry.
     *
     * @return sex
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Changes the sex of this cv entry to the given value.
     *
     * @param sex The new value
     * @return Entry this
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Retrieves the phoneNumber of this entry.
     *
     * @return phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Changes the phoneNumber of this cv entry to the given value.
     *
     * @param phoneNumber The new value
     * @return Entry this
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Retrieves the email of this entry.
     *
     * @return email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Changes the email of this cv entry to the given value.
     *
     * @param email The new value
     * @return Entry this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retrieves the address of this entry.
     *
     * @return address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Changes the address of this cv entry to the given value.
     *
     * @param address The new value
     * @return Entry this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Retrieves the priorStudy of this entry.
     *
     * @return priorStudy
     */
    public function getPriorStudy()
    {
        return $this->priorStudy;
    }

    /**
     * Changes the priorStudy of this cv entry to the given value.
     *
     * @param priorStudy The new value
     * @return Entry this
     */
    public function setPriorStudy($priorStudy)
    {
        $this->priorStudy = $priorStudy;

        return $this;
    }

    /**
     * Retrieves the priorGrade of this entry.
     *
     * @return priorGrade
     */
    public function getPriorGrade()
    {
        return $this->priorGrade;
    }

    /**
     * Changes the priorGrade of this cv entry to the given value.
     *
     * @param priorGrade The new value
     * @return Entry this
     */
    public function setPriorGrade($priorGrade)
    {
        $this->priorGrade = $priorGrade;

        return $this;
    }

    /**
     * Retrieves the study of this entry.
     *
     * @return study
     */
    public function getStudy()
    {
        return $this->study;
    }

    /**
     * Changes the study of this cv entry to the given value.
     *
     * @param study The new value
     * @return Entry this
     */
    public function setStudy($study)
    {
        $this->study = $study;

        return $this;
    }

    /**
     * Retrieves the grade of this entry.
     *
     * @return grade
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Changes the grade of this cv entry to the given value.
     *
     * @param grade The new value
     * @return Entry this
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Retrieves the bachelorStart of this entry.
     *
     * @return bachelorStart
     */
    public function getBachelorStart()
    {
        return $this->bachelorStart;
    }

    /**
     * Changes the bachelorStart of this cv entry to the given value.
     *
     * @param bachelorStart The new value
     * @return Entry this
     */
    public function setBachelorStart($bachelorStart)
    {
        $this->bachelorStart = $bachelorStart;

        return $this;
    }

    /**
     * Retrieves the bachelorEnd of this entry.
     *
     * @return bachelorEnd
     */
    public function getBachelorEnd()
    {
        return $this->bachelorEnd;
    }

    /**
     * Changes the bachelorEnd of this cv entry to the given value.
     *
     * @param bachelorEnd The new value
     * @return Entry this
     */
    public function setBachelorEnd($bachelorEnd)
    {
        $this->bachelorEnd = $bachelorEnd;

        return $this;
    }

    /**
     * Retrieves the masterStart of this entry.
     *
     * @return masterStart
     */
    public function getMasterStart()
    {
        return $this->masterStart;
    }

    /**
     * Changes the masterStart of this cv entry to the given value.
     *
     * @param masterStart The new value
     * @return Entry this
     */
    public function setMasterStart($masterStart)
    {
        $this->masterStart = $masterStart;

        return $this;
    }

    /**
     * Retrieves the masterEnd of this entry.
     *
     * @return masterEnd
     */
    public function getMasterEnd()
    {
        return $this->masterEnd;
    }

    /**
     * Changes the masterEnd of this cv entry to the given value.
     *
     * @param masterEnd The new value
     * @return Entry this
     */
    public function setMasterEnd($masterEnd)
    {
        $this->masterEnd = $masterEnd;

        return $this;
    }

    /**
     * Retrieves the additionalDiplomas of this entry.
     *
     * @return additionalDiplomas
     */
    public function getAdditionalDiplomas()
    {
        return $this->additionalDiplomas;
    }

    /**
     * Changes the additionalDiplomas of this cv entry to the given value.
     *
     * @param additionalDiplomas The new value
     * @return Entry this
     */
    public function setAdditionalDiplomas($additionalDiplomas)
    {
        $this->additionalDiplomas = $additionalDiplomas;

        return $this;
    }

    /**
     * Retrieves the erasmusPeriod of this entry.
     *
     * @return erasmusPeriod
     */
    public function getErasmusPeriod()
    {
        return $this->erasmusPeriod;
    }

    /**
     * Changes the erasmusPeriod of this cv entry to the given value.
     *
     * @param erasmusPeriod The new value
     * @return Entry this
     */
    public function setErasmusPeriod($erasmusPeriod)
    {
        $this->erasmusPeriod = $erasmusPeriod;

        return $this;
    }

    /**
     * Retrieves the erasmusLocation of this entry.
     *
     * @return erasmusLocation
     */
    public function getErasmusLocation()
    {
        return $this->erasmusLocation;
    }

    /**
     * Changes the erasmusLocation of this cv entry to the given value.
     *
     * @param erasmusLocation The new value
     * @return Entry this
     */
    public function setErasmusLocation($erasmusLocation)
    {
        $this->erasmusLocation = $erasmusLocation;

        return $this;
    }

    /**
     * Retrieves the - of this entry.
     *
     * @return -
     */
    public function getLanguages()
    {
        return $this->languages->toArray();
    }

    /**
     * Retrieves the languageExtra of this entry.
     *
     * @return languageExtra
     */
    public function getLanguageExtra()
    {
        return $this->languageExtra;
    }

    /**
     * Changes the languageExtra of this cv entry to the given value.
     *
     * @param languageExtra The new value
     * @return Entry this
     */
    public function setLanguageExtra($languageExtra)
    {
        $this->languageExtra = $languageExtra;

        return $this;
    }

    /**
     * Retrieves the computerSkills of this entry.
     *
     * @return computerSkills
     */
    public function getComputerSkills()
    {
        return $this->computerSkills;
    }

    /**
     * Changes the computerSkills of this cv entry to the given value.
     *
     * @param computerSkills The new value
     * @return Entry this
     */
    public function setComputerSkills($computerSkills)
    {
        $this->computerSkills = $computerSkills;

        return $this;
    }

    /**
     * Retrieves the experiences of this entry.
     *
     * @return experiences
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * Changes the experiences of this cv entry to the given value.
     *
     * @param experiences The new value
     * @return Entry this
     */
    public function setExperiences($experiences)
    {
        $this->experiences = $experiences;

        return $this;
    }

    /**
     * Retrieves the thesisSummary of this entry.
     *
     * @return thesisSummary
     */
    public function getThesisSummary()
    {
        return $this->thesisSummary;
    }

    /**
     * Changes the thesisSummary of this cv entry to the given value.
     *
     * @param thesisSummary The new value
     * @return Entry this
     */
    public function setThesisSummary($thesisSummary)
    {
        $this->thesisSummary = $thesisSummary;

        return $this;
    }

    /**
     * Retrieves the futureInterest of this entry.
     *
     * @return futureInterest
     */
    public function getFutureInterest()
    {
        return $this->futureInterest;
    }

    /**
     * Changes the futureInterest of this cv entry to the given value.
     *
     * @param futureInterest The new value
     * @return Entry this
     */
    public function setFutureInterest($futureInterest)
    {
        $this->futureInterest = $futureInterest;

        return $this;
    }

    /**
     * Retrieves the mobilityEurope of this entry.
     *
     * @return mobilityEurope
     */
    public function getMobilityEurope()
    {
        return $this->mobilityEurope;
    }

    /**
     * Changes the mobilityEurope of this cv entry to the given value.
     *
     * @param mobilityEurope The new value
     * @return Entry this
     */
    public function setMobilityEurope($mobilityEurope)
    {
        $this->mobilityEurope = $mobilityEurope;

        return $this;
    }

    /**
     * Retrieves the mobilityWorld of this entry.
     *
     * @return mobilityWorld
     */
    public function getMobilityWorld()
    {
        return $this->mobilityWorld;
    }

    /**
     * Changes the mobilityWorld of this cv entry to the given value.
     *
     * @param mobilityWorld The new value
     * @return Entry this
     */
    public function setMobilityWorld($mobilityWorld)
    {
        $this->mobilityWorld = $mobilityWorld;

        return $this;
    }

    /**
     * Retrieves the careerExpectations of this entry.
     *
     * @return careerExpectations
     */
    public function getCareerExpectations()
    {
        return $this->careerExpectations;
    }

    /**
     * Changes the careerExpectations of this cv entry to the given value.
     *
     * @param careerExpectations The new value
     * @return Entry this
     */
    public function setCareerExpectations($careerExpectations)
    {
        $this->careerExpectations = $careerExpectations;

        return $this;
    }

    /**
     * Retrieves the hobbies of this entry.
     *
     * @return hobbies
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * Changes the hobbies of this cv entry to the given value.
     *
     * @param hobbies The new value
     * @return Entry this
     */
    public function setHobbies($hobbies)
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    /**
     * Retrieves the about of this entry.
     *
     * @return about
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Changes the about of this cv entry to the given value.
     *
     * @param about The new value
     * @return Entry this
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Retrieves the id of this entry.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieves the academic of this entry.
     *
     * @return academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * Changes the academic of this cv entry to the given value.
     *
     * @param academic The new value
     * @return Entry this
     */
    public function setAcademic($academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * Retrieves the year of this entry.
     *
     * @return year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Changes the year of this cv entry to the given value.
     *
     * @param year The new value
     * @return Entry this
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

}
