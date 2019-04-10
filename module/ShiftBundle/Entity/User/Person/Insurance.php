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

namespace ShiftBundle\Entity\User\Person;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores shift information for a person in a certain academic year.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\User\Person\Insurance")
 * @ORM\Table(
 *      name="users_people_shift_insurance",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="insurance_unique", columns={"person", "academic_year"})})
 */
class Insurance
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person associated with this entity
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean Flag whether this person has read the insurance
     *
     * @ORM\Column(name="has_read_insurance", type="boolean")
     */
    private $hasReadInsurance;

    /**
     * @var AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param Person       $person           The person associated with this entity
     * @param boolean      $hasReadInsurance Flag whether this person read the insurance info
     * @param AcademicYear $academicYear     The acadmic year when this is read.
     */
    public function __construct(Person $person, $hasReadInsurance, AcademicYear $academicYear)
    {
        $this->person = $person;
        $this->hasReadInsurance = $hasReadInsurance;
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
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function hasReadInsurance()
    {
        return $this->hasReadInsurance;
    }

    /**
     * @param boolean $hasReadInsurance
     *
     * @return self
     */
    public function setHasReadInsurance($hasReadInsurance)
    {
        $this->hasReadInsurance = $hasReadInsurance;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
