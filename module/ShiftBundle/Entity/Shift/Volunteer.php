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

namespace ShiftBundle\Entity\Shift;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a volunteer for a shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift\Volunteer")
 * @ORM\Table(name="shifts.volunteers")
 */
class Volunteer
{
    /**
     * @var integer The ID of this Volunteer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The signup time
     *
     * @ORM\Column(name="signup_time", type="datetime")
     */
    private $signupTime;

    /**
     * @var Person The person that volunteered
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean Whether or not this volunteer has been payed already
     *
     * @ORM\Column(type="boolean")
     */
    private $payed;

    /**
     */
    public function __construct(Person $person,boolean $payed = null)
    {
        $this->signupTime = new DateTime();
        $this->person = $person;
        if ($payed == null)
            $payed = false;
        $this->payed = $payed;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getSignupTime()
    {
        return $this->signupTime;
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
    public function isPayed()
    {
        return $this->payed;
    }

    /**
     * @param  boolean $payed
     * @return self
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }
}
