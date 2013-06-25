<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Entity\User\Status;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the different types of memberships the organization has.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Users\Statuses\Organization")
 * @ORM\Table(name="users.organization_statuses")
 */
class Organization
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    public static $possibleStatuses = array(
        'member'            => 'Member',
        'non_member'        => 'Non-Member',
        'honorary_member'   => 'Honorary Member',
        'supportive_member' => 'Supportive Member',
        'praesidium'        => 'Praesidium'
    );

    /**
     * @var int The ID of this union status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The person this union status describes
     *
     * @ORM\ManyToOne(
     *         targetEntity="CommonBundle\Entity\User\Person", inversedBy="organizationStatuses"
     * )
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The actual status value
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the status
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param \CommonBundle\Entity\User\Person $person The person this union status describes
     * @param string $status The actual status value
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the status
     * @throws \InvalidArgumentException
     */
    public function __construct(Person $person, $status, AcademicYear $academicYear)
    {
        if(!self::isValidPerson($person, $academicYear))
            throw new \InvalidArgumentException('Invalid person');

        $this->person = $person;

        $this->setStatus($status);
        $this->academicYear = $academicYear;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given person can have a UnionStatus.
     *
     * @static
     * @param \CommonBundle\Entity\User\Person $person The person to check
     * @return bool
     */
    public static function isValidPerson(Person $person, AcademicYear $academicYear)
    {
        return ($person != null) && $person->canHaveOrganizationStatus($academicYear);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return \CommonBundle\Entity\User\UnionStatus
     */
    public function setStatus($status)
    {
        if (self::isValidStatus($status))
            $this->status = $status;

        return $this;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param $status string A status
     * @return bool
     */
    public static function isValidStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
