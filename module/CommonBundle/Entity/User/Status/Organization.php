<?php

namespace CommonBundle\Entity\User\Status;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Specifying the different types of memberships the organization has.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Status\Organization")
 * @ORM\Table(name="users_statuses_organization")
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
        'praesidium'        => 'Praesidium',
    );

    /**
     * @var integer The ID of this union status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person this union status describes
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
     * @var AcademicYear The year of the status
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param  Person       $person       The person this union status describes
     * @param  string       $status       The actual status value
     * @param  AcademicYear $academicYear The year of the status
     * @throws InvalidArgumentException
     */
    public function __construct(Person $person, $status, AcademicYear $academicYear)
    {
        if (!self::isValidPerson($person, $academicYear)) {
            throw new InvalidArgumentException('Invalid person');
        }

        $this->person = $person;

        $this->setStatus($status);
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
     * Returns whether the given person can have a UnionStatus.
     *
     * @static
     * @param  Person $person The person to check
     * @return boolean
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
     * @param  string $status
     * @return self
     */
    public function setStatus($status)
    {
        if (self::isValidStatus($status)) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param  string $status string A status
     * @return boolean
     */
    public static function isValidStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
