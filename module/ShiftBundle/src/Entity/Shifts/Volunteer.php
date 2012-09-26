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

namespace ShiftBundle\Entity\Shifts;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a volunteer for a shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shifts\Volunteer")
 * @ORM\Table(name="shifts.volunteers")
 */
class Volunteer
{
    /**
     * @var integer The ID of this unit
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The unit's name
     *
     * @ORM\Column(name="signup_time", type="datetime")
     */
    private $signupTime;

    /**
     * @var \CommonBundle\Entity\Users\Person The person that volunteered
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @param string $name
     */
    public function __construct(Person $person)
    {
        $this->signupTime = new DateTime();
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getSignupTime()
    {
        return $this->signupTime;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
