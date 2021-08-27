<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Driver")
 * @ORM\Table(name="logistics_drivers")
 */
class Driver
{
    /**
     * @var Person The person this driver represents
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var ArrayCollection The years during which the person was a driver
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist"})
     * @ORM\JoinTable(name="logistics_drivers_years_map",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="person")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="year_id", referencedColumnName="id")}
     * )
     */
    private $years;

    /**
     * @var string The color for used for this driver
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $color;

    /**
     * @var boolean The flag this driver is active of not
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @param Person                          $person The person to mark as a driver
     * @param $color The color for this driver
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->removed = false;

        $this->years = new ArrayCollection();
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return array
     */
    public function getYears()
    {
        return $this->years->toArray();
    }

    /**
     * @param  array $years
     * @return self
     */
    public function setYears(array $years)
    {
        $this->years = new ArrayCollection($years);

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        if ($this->color) {
            return $this->color;
        } else {
            return '#888888';
        }
    }

    /**
     * @param  string $color
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;

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
     * @param  boolean $removed
     * @return self
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

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
}
