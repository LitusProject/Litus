<?php

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company;
use CommonBundle\Component\Util\StringUtil;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Parsedown;

/**
 * This is the entity for an job.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Job")
 * @ORM\Table(name="br_companies_jobs")
 */
class Job
{
    /**
     * @var string The job's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The job's name
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string The description of the job
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string The benefits of the company.
     *
     * @ORM\Column(type="text")
     */
    private $benefits;

    /**
     * @var string The profile required for this job.
     *
     * @ORM\Column(type="text")
     */
    private $profile;

    /**
     * @var string The mailto link for this job
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;

    /**
     * @var string The city where the job is located
     *
     * @ORM\Column(type="text")
     */
    private $city;

    /**
     * @var string The location (eg. province) where the job is located
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $location;

    /**
     * @var string The masters for which this job is meant
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $master;

    /**
     * @var Company The company of the job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var string The type of the job
     *
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @var DateTime The last time this job was updated.
     *
     * @ORM\Column(type="date")
     */
    private $dateUpdated;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var string The sectors of the company
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sector;

    /**
     * @var boolean If this job has been approved by our BR team
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $approved;

    /**
     * @var boolean If this job has been removed.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $removed;

    /**
     * @static
     * @var array All the possible types allowed
     */
    public static $possibleTypes = array(
        'internship'  => 'Internship',
        'vacancy'     => 'Vacancy',
        'student job' => 'Student Job',
    );

    /**
     * @param Company $company The job's company
     * @param string  $type    The job's type (entry of $possibleTypes)
     */
    public function __construct(Company $company, $type)
    {
        $this->type = $type;
        $this->company = $company;
        $this->dateUpdated = new DateTime();
        $this->removed = false;
    }

    /**
     * @return self
     */
    public function approve()
    {
        $this->approved = true;

        return $this;
    }

    /**
     * @return self
     */
    public function pending()
    {
        $this->approved = false;

        return $this;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->removed = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        if ($this->approved === null) {
            return true;
        }

        return $this->approved;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $name
     * @return Job
     */
    public function setName($name)
    {
        if ($name === null || !is_string($name)) {
            throw new InvalidArgumentException('Invalid name');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $type
     * @return Job
     */
    public function setType($type)
    {
        if ($type === null || !is_string($type)) {
            throw new InvalidArgumentException('Invalid type');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return self::$possibleTypes[$this->type];
    }

    /**
     * @param  string $description
     * @return Job
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string $benefits
     * @return Job
     */
    public function setBenefits($benefits)
    {
        $this->benefits = $benefits;

        return $this;
    }

    /**
     * @return string
     */
    public function getBenefits()
    {
        return $this->benefits;
    }

    /**
     * @param  string $profile
     * @return Job
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param  string $email
     * @return Job
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string $city
     * @return Job
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getSummary($length = 50)
    {
        $parsedown = new Parsedown();
        $summary = $parsedown->text($this->getDescription());

        return StringUtil::truncate($summary, $length, '...');
    }

    /**
     * @return string
     */
    public function getSummaryStriped($length = 50)
    {
        return strip_tags($this->getSummary($length));
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return Job
     */
    public function updateDate()
    {
        $this->dateUpdated = new DateTime();

        return $this;
    }

    /**
     * @return DateTime The last time this job was updated.
     */
    public function getLastUpdateDate()
    {
        return $this->dateUpdated;
    }

    /**
     * @param  DateTime $startDate
     * @return Job
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return Job
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string|null
     */
    public function getMaster()
    {
        if (!$this->master || substr($this->master, 0, 1) == 'N') {
            return null;
        }
        $mastersArray = array();
        if (substr($this->master, 0, 2) == 'a:') {
            $masters = unserialize($this->master);
            if ($masters !== false) {
                foreach ($masters as $master) {
                    array_push($mastersArray, Company::POSSIBLE_MASTERS[$master]);
                }
            }
            return implode(', ', $mastersArray);
        } else {
            return Company::POSSIBLE_MASTERS[$this->master];
        }
    }

    /**
     * @param  array|null $masters
     * @return Job
     */
    public function setMaster($masters)
    {
        if (!$masters) {
            $this->master = null;
        }

        if (!is_string($masters)) {
            $masters = serialize($masters);
        }

        $this->master = $masters;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMasterCode()
    {
        if (!$this->master || substr($this->master, 0, 1) == 'N') {
            return null;
        }

        $unserializedMasters = unserialize($this->master);

        foreach ($unserializedMasters as $master) {
            if (!Company::isValidMaster($master)) {
                throw new InvalidArgumentException('The master is not valid.');
            }
        }
        return $unserializedMasters;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        if ($this->location === null) {
            return null;
        }

        return Company::POSSIBLE_LOCATIONS[$this->location];
    }

    /**
     * @param  string $location
     * @return Job
     */
    public function setLocation($location)
    {
        if (!Company::isValidLocation($location)) {
            throw new InvalidArgumentException('The location is not valid.');
        }

        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocationCode()
    {
        return $this->location;
    }

    /**
     * @return string|null
     */
    public function getSector()
    {
        if (!$this->sector) {
            return null;
        }
        $sectorsArray = array();
        if (substr($this->sector, 0, 2) === 'a:') {
            $sectors = unserialize($this->sector);
            if ($sectors !== false) {
                foreach ($sectors as $sector) {
                    array_push($sectorsArray, Company::POSSIBLE_SECTORS[$sector]);
                }
            }
            return implode(', ', $sectorsArray);
        } else {
            return Company::POSSIBLE_SECTORS[$this->sector];
        }
    }

    /**
     * @param  array $sectors
     * @return Job
     */
    public function setSector($sectors)
    {
        if (!is_string($sectors)) {
            $sectors = serialize($sectors);
        }

        $this->sector = $sectors;

        return $this;
    }

    /**
     * @return string|array|null
     */
    public function getSectorCode()
    {
        if (!$this->sector) {
            return null;
        }
        $sectorsArray = array();
        if (substr($this->sector, 0, 2) === 'a:') {
            $unserializedSectors = unserialize($this->sector);
            if ($unserializedSectors !== false) {
                foreach ($unserializedSectors as $sector) {
                    if (!Company::isValidsector($sector)) {
                        throw new InvalidArgumentException('The sector is not valid.');
                    }
                    array_push($sectorsArray, $sector);
                }
            }
            return $sectorsArray;
        } else {
            return $this->sector;
        }
    }
}
