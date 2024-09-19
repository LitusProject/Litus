<?php

namespace FakBundle\Entity;

use CommonBundle\Component\Form\Admin\Element\DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the Scanner Logs
 *
 * @ORM\Entity(repositoryClass="FakBundle\Repository\Log")
 * @ORM\Table(name="fak_log")
 */
class Log
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
     * @var string The r-number of the person who has scanned
     *
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_double", type="boolean")
     */
    private $isDouble;

    /**
     * Log constructor
     * @param string    $username
     * @param \DateTime $time
     * @param boolean   $isDouble
     */
    public function __construct(string $username, \DateTime $time, bool $isDouble = false)
    {
        $this->username = $username;
        $this->time = $time;
        $this->isDouble = $isDouble;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return self
     */
    public function setUserName($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDouble()
    {
        return $this->isDouble;
    }

    /**
     * @param boolean $isDouble
     * @return self
     */
    public function setIsDouble($isDouble)
    {
        $this->isDouble = $isDouble;
        return $this;
    }
}
