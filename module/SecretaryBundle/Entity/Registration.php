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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Registration")
 * @ORM\Table(name="secretary_registrations")
 */
class Registration
{
    /**
     * @var integer The ID of the registration
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person of the registration
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The academic year of the registration
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var DateTime The time of the registration
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
     * @var DateTime|null The time of the registration payement
     *
     * @ORM\Column(name="payed_timestamp", type="datetime", nullable=true)
     */
    private $payedTimestamp;

    /**
     * @var boolean Flag whether this registration has been cancelled
     *
     * @ORM\Column(type="boolean")
     */
    private $cancelled;

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     */
    public function __construct(Academic $academic, AcademicYear $academicYear)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->timestamp = new DateTime();
        $this->payed = false;
        $this->cancelled = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return DateTime
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
     * @return DateTime|null
     */
    public function getPayedTimestamp()
    {
        return $this->payedTimestamp;
    }

    /**
     * @param  boolean $payed
     * @return self
     */
    public function setPayed($payed = true)
    {
        $this->payed = $payed;
        $this->payedTimestamp = $payed ? new DateTime() : null;

        return $this;
    }

    public function isCancelled()
    {
        return $this->cancelled;
    }

    public function setCancelled($cancel = false)
    {
        $this->cancelled = $cancel;

        return $this;
    }
}
