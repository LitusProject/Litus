<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Driver")
 * @ORM\Table(name="logistics.drivers")
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
     * @ORM\JoinTable(name="logistics.drivers_years",
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
     * @param Person $person The person to mark as a driver
     * @param $color The color for this driver
     */
    public function __construct(Person $person, $color)
    {
        $this->person = $person;
        $this->color = $color;
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
        if ($this->color)
            return $this->color;
        else
            return '#888888';
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
     * @param  boolean $removed
     * @return self
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }
}
