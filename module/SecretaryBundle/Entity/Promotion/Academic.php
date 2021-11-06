<?php

namespace SecretaryBundle\Entity\Promotion;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic as AcademicPerson;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a promotion.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion\Academic")
 * @ORM\Table(name="secretary_promotions_academic")
 */
class Academic extends \SecretaryBundle\Entity\Promotion
{
    /**
     * @var AcademicPerson The academic associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new promotion with the given academic.
     *
     * @param AcademicYear   $academicYear The academic year for this promotion.
     * @param AcademicPerson $academic     The academic to add.
     */
    public function __construct(AcademicYear $academicYear, AcademicPerson $academic)
    {
        parent::__construct($academicYear);

        $this->academic = $academic;
    }

    /**
     * @return AcademicPerson
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->academic->getFullName();
    }
}
