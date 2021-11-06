<?php

namespace ShiftBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a token used to generate a vCalendar so that we can create
 * an iCal file even when nobody is logged in.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Token")
 * @ORM\Table(
 *     name="shift_tokens",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="shift_tokens_hash", columns={"hash"})}
 * )
 */
class Token
{
    /**
     * @var integer The ID of this token
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The person associated with this token
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The token's hash
     *
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->hash = md5(uniqid(rand(), true));
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
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param  string $hash
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }
}
