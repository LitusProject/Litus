<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Entity;

use CommonBundle\Entity\Users\People\Academic,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Registration")
 * @ORM\Table(name="users.registration")
 */
class Registration
{
    /**
     * @var int The ID of this registration
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The person of this registration
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The academic year of this registration
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \DateTime The time of the registration
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var boolean Flag whether the person has payed
     *
     * @ORM\Column(type="boolean")
     */
    private $payed;

    /**
     * @var \DateTime The time of the registration payement
     *
     * @ORM\Column(name="payed_timestamp", type="datetime", nullable=true)
     */
    private $payedTimestamp;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $academic
     */
    public function __construct(Academic $academic)
    {
        $this->academic = $academic;
        $this->timestamp = new DateTime();
        $this->payed = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return boolean
     */
    public function hasPayed()
    {
        return $this->payed;
    }

    /**
     * @return \DateTime
     */
    public function getPayedTimestamp()
    {
        return $this->payedTimestamp;
    }

    /**
     * @return \SecretaryBundle\Entity\Registration
     */
    public function setPayed()
    {
        $this->payed = true;
        $this->payedTimestamp = new DateTime();
        return $this;
    }
}
