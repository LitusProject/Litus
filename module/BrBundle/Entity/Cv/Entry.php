<?php

namespace BrBundle\Entity\Cv;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Address;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study;

/**
 * This is the entity for a cv entry.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Cv\Entry")
 * @ORM\Table(
 *     name="br_cv_entries",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="br_cv_entries_year_academic", columns={"year", "academic"})}
 * )
 */
class Entry
{
    /**
     * @var integer The entry's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person\Academic The academic to whom this cv belongs
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
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
     * @var string The persons sex ('m', 'f' or 'x')
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
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
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
     * @var integer The prior grade
     *
     * @ORM\Column(name="prior_grade", type="bigint")
     */
    private $priorGrade;

    /**
     * @var Study The study of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(name="study", referencedColumnName="id", onDelete="CASCADE")
     */
    private $study;

    /**
     * @var integer The grade of the current study.
     *
     * @ORM\Column(name="grade", type="bigint")
     */
    private $grade;

    /**
     * @var integer The user's personal email
     *
     * @ORM\Column(name="bachelor_start", type="integer")
     */
    private $bachelorStart;

    /**
     * @var integer The user's personal email
     *
     * @ORM\Column(name="bachelor_end", type="integer")
     */
    private $bachelorEnd;

    /**
     * @var integer The user's personal email
     *
     * @ORM\Column(name="master_start", type="integer")
     */
    private $masterStart;

    /**
     * @var integer The user's personal email
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
     * @var \Doctrine\Common\Collections\ArrayCollection The experiences added to this cv
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Cv\Experience", mappedBy="entry", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "ASC"})
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
     * @param Academic     $academic The academic
     * @param AcademicYear $year     The current academic year.
     */
    public function __construct(Academic $academic, AcademicYear $year)
    {
        $this->academic = $academic;
        $this->year = $year;
    }

    /**
     * Retrieves the firstName of this entry.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Changes the firstName of this cv entry to the given value.
     *
     * @param  string $firstName The new value
     * @return Entry
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Retrieves the lastName of this entry.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Changes the lastName of this cv entry to the given value.
     *
     * @param  string $lastName The new value
     * @return Entry
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Retrieves the birthday of this entry.
     *
     * @return DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Changes the birthday of this cv entry to the given value.
     *
     * @param  DateTime $birthday The new value
     * @return Entry
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Retrieves the sex of this entry.
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Changes the sex of this cv entry to the given value.
     *
     * @param  string $sex The new value
     * @return Entry
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Retrieves the phoneNumber of this entry.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Changes the phoneNumber of this cv entry to the given value.
     *
     * @param  string $phoneNumber The new value
     * @return Entry
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Retrieves the email of this entry.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Changes the email of this cv entry to the given value.
     *
     * @param  string $email The new value
     * @return Entry
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retrieves the address of this entry.
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Changes the address of this cv entry to the given value.
     *
     * @param  Address $address The new value
     * @return Entry
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Retrieves the priorStudy of this entry.
     *
     * @return string
     */
    public function getPriorStudy()
    {
        return $this->priorStudy;
    }

    /**
     * Changes the priorStudy of this cv entry to the given value.
     *
     * @param  string $priorStudy The new value
     * @return Entry
     */
    public function setPriorStudy($priorStudy)
    {
        $this->priorStudy = $priorStudy;

        return $this;
    }

    /**
     * Retrieves the priorGrade of this entry.
     *
     * @return integer
     */
    public function getPriorGrade()
    {
        return $this->priorGrade;
    }

    /**
     * Retrieves the priorGrade of this entry.
     *
     * @param array $map
     * @return string
     */
    public function getPriorGradeMapped($map)
    {
        ksort($map);
        foreach ($map as $key => $value) {
            if ($this->priorGrade < $key) {
                return $value;
            }
        }

        return 'Unspecified';
    }

    /**
     * Changes the priorGrade of this cv entry to the given value.
     *
     * @param  integer $priorGrade The new value
     * @return Entry
     */
    public function setPriorGrade($priorGrade)
    {
        $this->priorGrade = $priorGrade;

        return $this;
    }

    /**
     * Retrieves the study of this entry.
     *
     * @return Study
     */
    public function getStudy()
    {
        return $this->study;
    }

    /**
     * Changes the study of this cv entry to the given value.
     *
     * @param  Study $study The new value
     * @return Entry
     */
    public function setStudy(Study $study)
    {
        $this->study = $study;

        return $this;
    }

    /**
     * Retrieves the grade of this entry.
     *
     * @return integer
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Retrieves the priorGrade of this entry.
     *
     * @param array $map
     * @return string
     */
    public function getGradeMapped($map)
    {
        ksort($map);
        foreach ($map as $key => $value) {
            if ($this->grade < $key) {
                return $value;
            }
        }

        return 'Unspecified';
    }

    /**
     * Changes the grade of this cv entry to the given value.
     *
     * @param  integer $grade The new value
     * @return Entry
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Retrieves the bachelorStart of this entry.
     *
     * @return integer
     */
    public function getBachelorStart()
    {
        return $this->bachelorStart;
    }

    /**
     * Changes the bachelorStart of this cv entry to the given value.
     *
     * @param  integer $bachelorStart The new value
     * @return Entry
     */
    public function setBachelorStart($bachelorStart)
    {
        $this->bachelorStart = $bachelorStart;

        return $this;
    }

    /**
     * Retrieves the bachelorEnd of this entry.
     *
     * @return integer
     */
    public function getBachelorEnd()
    {
        return $this->bachelorEnd;
    }

    /**
     * Changes the bachelorEnd of this cv entry to the given value.
     *
     * @param  integer $bachelorEnd The new value
     * @return Entry
     */
    public function setBachelorEnd($bachelorEnd)
    {
        $this->bachelorEnd = $bachelorEnd;

        return $this;
    }

    /**
     * Retrieves the masterStart of this entry.
     *
     * @return integer
     */
    public function getMasterStart()
    {
        return $this->masterStart;
    }

    /**
     * Changes the masterStart of this cv entry to the given value.
     *
     * @param  integer $masterStart The new value
     * @return Entry
     */
    public function setMasterStart($masterStart)
    {
        $this->masterStart = $masterStart;

        return $this;
    }

    /**
     * Retrieves the masterEnd of this entry.
     *
     * @return integer
     */
    public function getMasterEnd()
    {
        return $this->masterEnd;
    }

    /**
     * Changes the masterEnd of this cv entry to the given value.
     *
     * @param  integer $masterEnd The new value
     * @return Entry
     */
    public function setMasterEnd($masterEnd)
    {
        $this->masterEnd = $masterEnd;

        return $this;
    }

    /**
     * Retrieves the additionalDiplomas of this entry.
     *
     * @return string
     */
    public function getAdditionalDiplomas()
    {
        return $this->additionalDiplomas;
    }

    /**
     * Changes the additionalDiplomas of this cv entry to the given value.
     *
     * @param  string $additionalDiplomas The new value
     * @return Entry
     */
    public function setAdditionalDiplomas($additionalDiplomas)
    {
        $this->additionalDiplomas = $additionalDiplomas;

        return $this;
    }

    /**
     * Retrieves the erasmusPeriod of this entry.
     *
     * @return string
     */
    public function getErasmusPeriod()
    {
        return $this->erasmusPeriod;
    }

    /**
     * Changes the erasmusPeriod of this cv entry to the given value.
     *
     * @param  string $erasmusPeriod The new value
     * @return Entry
     */
    public function setErasmusPeriod($erasmusPeriod)
    {
        $this->erasmusPeriod = $erasmusPeriod;

        return $this;
    }

    /**
     * Retrieves the erasmusLocation of this entry.
     *
     * @return string
     */
    public function getErasmusLocation()
    {
        return $this->erasmusLocation;
    }

    /**
     * Changes the erasmusLocation of this cv entry to the given value.
     *
     * @param  string $erasmusLocation The new value
     * @return Entry
     */
    public function setErasmusLocation($erasmusLocation)
    {
        $this->erasmusLocation = $erasmusLocation;

        return $this;
    }

    /**
     * Retrieves the languages of this entry.
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages->toArray();
    }

    /**
     * Retrieves the languageExtra of this entry.
     *
     * @return string
     */
    public function getLanguageExtra()
    {
        return $this->languageExtra;
    }

    /**
     * Changes the languageExtra of this cv entry to the given value.
     *
     * @param  string $languageExtra The new value
     * @return Entry
     */
    public function setLanguageExtra($languageExtra)
    {
        $this->languageExtra = $languageExtra;

        return $this;
    }

    /**
     * Retrieves the computerSkills of this entry.
     *
     * @return string
     */
    public function getComputerSkills()
    {
        return $this->computerSkills;
    }

    /**
     * Changes the computerSkills of this cv entry to the given value.
     *
     * @param  string $computerSkills The new value
     * @return Entry
     */
    public function setComputerSkills($computerSkills)
    {
        $this->computerSkills = $computerSkills;

        return $this;
    }

    /**
     * Retrieves the experiences of this entry.
     * If the entry has old experience return string.
     *
     * @return array|string
     */
    public function getExperiences()
    {
        if ($this->hasOldExperiences()) {
            $experiences = $this->experiences->toArray();

            return $experiences[0]->getFunction();
        } else {
            return $this->sortNewExperiences($this->experiences->toArray());
        }
    }

    /**
     * Sort the given list of experiences on start date
     *
     *@return array
     */
    private function sortNewExperiences(array $experiences)
    {
        $result = array();
        $sorted = count($experiences) == 0;

        while (!$sorted) {
            $nbExperiences = count($experiences);

            $indexSmallest = 0;
            for ($i = 0; $i < $nbExperiences; $i++) {
                if ($experiences[$i]->getEndYear() == $experiences[$indexSmallest]->getEndYear()) {
                    if ($experiences[$i]->getStartYear() > $experiences[$indexSmallest]->getStartYear()) {
                        $indexSmallest = $i;
                    } elseif ($experiences[$i]->getEndYear() >= $experiences[$indexSmallest]->getEndYear()) {
                        $indexSmallest = $i;
                    }
                }
            }

            $result[] = $experiences[$indexSmallest];
            array_splice($experiences, $indexSmallest, 1);

            $sorted = count($experiences) == 0;
        }

        return $result;
    }

    /**
     * Check if this entry has a single experience with only a function filled in.
     * This experience is a result of the old structure of the CV.
     *
     * @return boolean
     */
    public function hasOldExperiences()
    {
        $experiences = $this->experiences->toArray();

        if (count($experiences) != 1) {
            return false;
        }

        return $experiences[0]->getType() === null && $experiences[0]->getStartYear() === null && $experiences[0]->getEndYear() === null;
    }

    /**
     * Retrieves the thesisSummary of this entry.
     *
     * @return string
     */
    public function getThesisSummary()
    {
        return $this->thesisSummary;
    }

    /**
     * Changes the thesisSummary of this cv entry to the given value.
     *
     * @param  string $thesisSummary The new value
     * @return Entry
     */
    public function setThesisSummary($thesisSummary)
    {
        $this->thesisSummary = $thesisSummary;

        return $this;
    }

    /**
     * Retrieves the futureInterest of this entry.
     *
     * @return string
     */
    public function getFutureInterest()
    {
        return $this->futureInterest;
    }

    /**
     * Changes the futureInterest of this cv entry to the given value.
     *
     * @param  string $futureInterest The new value
     * @return Entry
     */
    public function setFutureInterest($futureInterest)
    {
        $this->futureInterest = $futureInterest;

        return $this;
    }

    /**
     * Retrieves the mobilityEurope of this entry.
     *
     * @return string
     */
    public function getMobilityEurope()
    {
        return $this->mobilityEurope;
    }

    /**
     * Changes the mobilityEurope of this cv entry to the given value.
     *
     * @param  string $mobilityEurope The new value
     * @return Entry
     */
    public function setMobilityEurope($mobilityEurope)
    {
        $this->mobilityEurope = $mobilityEurope;

        return $this;
    }

    /**
     * Retrieves the mobilityWorld of this entry.
     *
     * @return string
     */
    public function getMobilityWorld()
    {
        return $this->mobilityWorld;
    }

    /**
     * Changes the mobilityWorld of this cv entry to the given value.
     *
     * @param  string $mobilityWorld The new value
     * @return Entry
     */
    public function setMobilityWorld($mobilityWorld)
    {
        $this->mobilityWorld = $mobilityWorld;

        return $this;
    }

    /**
     * Retrieves the careerExpectations of this entry.
     *
     * @return string
     */
    public function getCareerExpectations()
    {
        return $this->careerExpectations;
    }

    /**
     * Changes the careerExpectations of this cv entry to the given value.
     *
     * @param  string $careerExpectations The new value
     * @return Entry
     */
    public function setCareerExpectations($careerExpectations)
    {
        $this->careerExpectations = $careerExpectations;

        return $this;
    }

    /**
     * Retrieves the hobbies of this entry.
     *
     * @return string
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * Changes the hobbies of this cv entry to the given value.
     *
     * @param  string $hobbies The new value
     * @return Entry
     */
    public function setHobbies($hobbies)
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    /**
     * Retrieves the about of this entry.
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Changes the about of this cv entry to the given value.
     *
     * @param  string $about The new value
     * @return Entry
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Retrieves the id of this entry.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieves the academic of this entry.
     *
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * Changes the academic of this cv entry to the given value.
     *
     * @param  Academic $academic The new value
     * @return Entry
     */
    public function setAcademic($academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * Retrieves the year of this entry.
     *
     * @return AcademicYear
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Changes the year of this cv entry to the given value.
     *
     * @param  AcademicYear $year The new value
     * @return Entry
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }
}
