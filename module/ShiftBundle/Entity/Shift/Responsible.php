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

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    DateTime,
    IllegalArgumentException,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a responsible for a shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift\Responsible")
 * @ORM\Table(name="shifts.responsibles")
 */
class Responsible
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
     * @var DateTime The unit's name
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
     * @param  Person                   $person
     * @param  AcademicYear             $academicYear
     * @throws IllegalArgumentException
     */
    public function __construct(Person $person, AcademicYear $academicYear)
    {
        $this->signupTime = new DateTime();

        if (!$person->isPraesidium($academicYear))
            throw new InvalidArgumentException('The given person cannot be a responsible');

        $this->person = $person;
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
}
