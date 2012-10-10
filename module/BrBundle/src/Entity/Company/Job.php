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
    Doctrine\ORM\Mapping as ORM;

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
    private static $_possibleStatuses = array(
        'internship', 'vacancy'
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
     * @var \BrBundle\Entity\Company The company of the job
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Company")
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
     * @param string $name The job's name
     * @param string $description The job's description
     * @param \BrBundle\Entity\Company $company The job's company
     * @param string $type The job's type (entry of $_possibleTypes)
     */
    public function __construct($name, $description, Company $company, $type)
    {
        $this->setName($name);
        $this->setDescription($description);

        $this->type = $type;
        $this->company = $company;
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
     * @return string
     */
    public function getSummary($length = 50)
    {
        return substr($this->description, 0, $length) . (strlen($this->description) > $length ? '...' : '');
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
