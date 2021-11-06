<?php

namespace BrBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a person that is a collaborator of corporate relations.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Collaborator")
 * @ORM\Table(name="br_collaborators")
 */
class Collaborator
{
    /**
     * @var integer The company's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The contract accompanying this order
     *
     * @ORM\OneToOne(targetEntity="\CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var integer Integer that resembles the personal number of the person.
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var boolean True if the current person is an active member of corporate relations.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->activate();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return self
     */
    public function activate()
    {
        $this->active = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return self
     */
    public function retire()
    {
        $this->active = false;

        return $this;
    }

    /**
     * @return self
     */
    public function rehire()
    {
        $this->active = true;

        return $this;
    }
}
