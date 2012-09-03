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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace LogisticsBundle\Entity;

use CommonBundle\Entity\Users\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Driver")
 * @ORM\Table(name="logistics.driver")
 */
class Driver
{

    /**
     * @var \CommonBundle\Entity\Users\Person The person this driver represents
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Users\Person", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * Creates a new driver for the given person
     * 
     * @param \CommonBundle\Entity\Users\Person $person The person to mark as a driver.
     */
    public function __construct(Person $person)
    {
        $this->setPerson($person);
    }

    /**
     * @param CommonBundle\Entity\Users\Person $person The person that this driver represents
     * @return LogisticsBundle\Entity\Driver
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;
        return $this;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person The person associated with this driver.
     */
    public function getPerson()
    {
        return $this->person;
    }

}