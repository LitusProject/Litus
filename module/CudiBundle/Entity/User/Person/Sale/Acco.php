<?php

namespace CudiBundle\Entity\User\Person\Sale;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores Acco information for a person.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\User\Person\Sale\Acco")
 * @ORM\Table(name="users_people_sale_acco")
 */
class Acco
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person associated with this entity
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean Flag whether this person has an acco card
     *
     * @ORM\Column(name="has_acco_card", type="boolean")
     */
    private $hasAccoCard;

    /**
     * @param Person  $person      The person associated with this entity
     * @param boolean $hasAccoCard Flag whether this person has an acco card
     */
    public function __construct(Person $person, $hasAccoCard)
    {
        $this->person = $person;
        $this->hasAccoCard = $hasAccoCard;
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
     * @return boolean
     */
    public function hasAccoCard()
    {
        return $this->hasAccoCard;
    }

    /**
     * @param boolean $hasAccoCard
     *
     * @return self
     */
    public function setHasAccoCard($hasAccoCard)
    {
        $this->hasAccoCard = $hasAccoCard;

        return $this;
    }
}
