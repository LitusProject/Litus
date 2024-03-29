<?php

namespace BrBundle\Entity;

use BrBundle\Entity\Company;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a communication option
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Communication")
 * @ORM\Table(name="br_communication")
 */

class Communication
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Company The company to which the company belongs
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company", cascade={"persist"})
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string the audience for the communication
     *
     * @ORM\Column(name="audience", type="string")
     */
    private $audience;

    /**
     * @var string The communication option
     *
     * @ORM\Column(name="option", type="string")
     */
    private $option;

    /**
     * @var Person The creator of this communication
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    private $creator;

    /**
     * Communication constructor.
     * @param Person $creator
     */
    public function __construct(Person $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return Person
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param \BrBundle\Entity\Company|null $company
     * @return Communication
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Communication
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * @param string $audience
     * @return Communication
     */
    public function setAudience(string $audience)
    {
        $this->audience = $audience;

        return $this;
    }

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param string $option
     * @return Communication
     */
    public function setOption(String $option)
    {
        $this->option = $option;

        return $this;
    }
}
