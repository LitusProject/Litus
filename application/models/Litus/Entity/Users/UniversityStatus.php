<?php

namespace Litus\Entity\Users;

use \Litus\Entity\Users\Person;
use \Litus\Util\AcademicYear;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\UniversityStatus")
 * @Table(name="users.university_statuses")
 */
class UniversityStatus
{
    /**
     * All the possible status values allowed.
     *
     * @var array
     */
    private static $POSSIBLE_STATUSES = array(
        'professor',
        'student',
        'alumnus',
        'external_student'
    );

    /**
     * The ID of this UniversityStatus.
     *
     * @var int
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * The Person this UniversityStatus belongs to.
     *
     * @var Person
     *
     * @Column(name="person")
     * @ManyToOne(
     *      targetEntity="Litus\Entity\Users\Academic", inversedBy="universityStatuses"
     * )
     */
    private $person;

    /**
     * The actual status value.
     *
     * @var string
     *
     * @Column(type="string")
     */
    private $status;

    /**
     * The academic year this status is/was valid in. The format is yyzz (i.e. 0910, 1112).
     *
     * @var string
     *
     * @Column(type="string", length="4")
     */
    private $year;

    /**
     * Constructing a new status.
     *
     * @throws \InvalidArgumentException
     * @param Person $person The person that should be given the status
     * @param string $status The status that should be given to the person
     */
    public function __construct(Person $person, $status)
    {
        if (!UniversityStatus::isValidPerson($person))
            throw new \InvalidArgumentException('Invalid person');
        $this->person = $person;
        
        $this->setStatus($status);
        $this->year = AcademicYear::getShortAcademicYear();
    }

    /**
     * Returns the ID of this UniversityStatus.
     *
     * @return int the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the Person this UniversityStatus belongs to..
     *
     * @return Person the person this UniversityStatus belongs to.
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a UniversityStatus.
     *
     * @static
     * @param Person $person the user to check
     * @return bool
     */
    public static function isValidPerson(Person $person)
    {
        return ($person != null) && $person->canHaveUniversityStatus();
    }

    /**
     * Returns the actual value.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status to the given value if valid.
     *
     * @see isValidStatus($status)
     * @param $status string the status to set
     * @return void doesn't return anything
     */
    public function setStatus($status)
    {
        if (self::isValidStatus($status))
            $this->status = $status;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param $status string a status
     * @return bool
     */
    public static function isValidStatus($status)
    {
        return (array_search($status, UniversityStatus::$POSSIBLE_STATUSES, true) != false);
    }

    /**
     * Returns the academic year of this UniversityStatus.
     *
     * @return string
     * @see \Litus\Util\AcademicYear::getShortAcademicYear(new DateTime('now'))
     */
    public function getYear()
    {
        return $this->year;
    }
}
