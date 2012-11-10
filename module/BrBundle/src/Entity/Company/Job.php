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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    Markdown_Parser;

/**
 * This is the entity for an job.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Job")
 * @ORM\Table(name="br.companies_job")
 */
class Job
{
    /**
     * @static
     * @var array All the possible types allowed
     */
    public static $possibleTypes = array(
        'internship' => 'Internship',
        'vacancy' => 'Vacancy',
    );

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
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string The description of the job
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string The profile required for this job.
     *
     * @ORM\Column(type="text")
     */
    private $profile;

    /**
     * @var string The required knowledge for this job.
     *
     * @ORM\Column(name="required_knowledge", type="text")
     */
    private $requiredKnowledge;

    /**
     * @var string The city where the job is located
     *
     * @ORM\Column(type="text")
     */
    private $city;

    /**
     * @var \BrBundle\Entity\Company The company of the job
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
     * @var string The last time this job was updated.
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
     * @var string The sector of the company
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $sector;

    /**
     * @param string $name The job's name
     * @param string $description The job's description
     * @param string $profile The job's profile
     * @param string $profile The job's required knowledge
     * @param \BrBundle\Entity\Company $company The job's company
     * @param string $type The job's type (entry of $possibleTypes)
     */
    public function __construct($name, $description, $profile, $requiredKnowledge, $city, Company $company, $type, $startDate, $endDate, $sector)
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setProfile($profile);
        $this->setRequiredKnowledge($requiredKnowledge);
        $this->setCity($city);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        $this->setSector($sector);

        $this->type = $type;
        $this->company = $company;
        $this->dateUpdated = new DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \BrBundle\Entity\Company\job
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

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
     * @param string $type
     * @return \BrBundle\Entity\Company\job
     */
    public function setType($type)
    {
        if ((null === $type) || !is_string($type))
            throw new \InvalidArgumentException('Invalid type');

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
        return Job::$possibleTypes[$this->type];
    }

    /**
     * @param string $description
     * @return \BrBundle\Entity\Company\job
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
     * @param string $profile
     * @return \BrBundle\Entity\Company\job
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
     * @param string $requiredKnowledge
     * @return \BrBundle\Entity\Company\job
     */
    public function setRequiredKnowledge($requiredKnowledge)
    {
        $this->requiredKnowledge = $requiredKnowledge;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequiredKnowledge()
    {
        return $this->requiredKnowledge;
    }

    /**
     * @param string $city
     * @return \BrBundle\Entity\Company\job
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
        $parser = new Markdown_Parser();
        $summary = $parser->transform($this->getDescription());
        return \CommonBundle\Component\Util\String::truncate($summary, $length, '...', true);
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return \BrBundle\Entity\Company\job
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
     * @param DateTime $startDate
     *
     * @return \BrBundle\Entity\Company\Job
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \BrBundle\Entity\Company\Job
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param string $sector
     * @return \BrBundle\Entity\Company\Job
     */
    public function setSector($sector)
    {
        if (!Company::isValidSector($sector))
            throw new \InvalidArgumentException('The sector is not valid.');
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return string
     */
    public function getSector()
    {
        return Company::$POSSIBLE_SECTORS[$this->sector];
    }

    /**
     * @return string
     */
    public function getSectorCode()
    {
        return $this->sector;
    }
}
