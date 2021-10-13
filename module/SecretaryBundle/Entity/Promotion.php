<?php

namespace SecretaryBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a promotion year.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion")
 * @ORM\Table(name="secretary_promotions")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="SecretaryBundle\Entity\Promotion\Academic",
 *      "external"="SecretaryBundle\Entity\Promotion\External"
 * })
 */
abstract class Promotion
{
    /**
     * @var integer The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var AcademicYear The year of the promotion
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * Creates a new promotion for the given year.
     *
     * @param AcademicYear $academicYear The academic year of this promotion.
     */
    public function __construct(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return string
     */
    abstract public function getEmailAddress();

    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();

    /**
     * @return string
     */
    abstract public function getFullName();
}
