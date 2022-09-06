<?php

namespace FakBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the Fak Scanner
 *
 * @ORM\Entity(repositoryClass="FakBundle\Repository\Scanner")
 * @ORM\Table(name="fak_scanner")
 */
class Scanner
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
     * @var string the R-number of the person who is checking in
     *
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @var integer|null The amount of check ins one person has
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * Scanner constructor
     */
    public function __construct($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string The username of the person to whom the check in belongs
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @param string $username The username of the person who's checking in
     * @return self
     */
    public function setUserName($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return int|null returns the amount of check ins one person has
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param integer $amount sets the amount of check ins
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param integer $number Adds a specified amount of check ins
     * @return self
     */
    public function addCheckin($number)
    {
        $this->amount += $number;
        return $this;
    }
}