<?php

namespace Litus\Entity\Users;

use Litus\Entity\Users\Person;
use Litus\Util\AcademicYear;
use \InvalidArgumentException;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\UnionStatus")
 * @Table(name="users.union_statuses")
 */
class UnionStatus
{
    /**
     * All the possible status values allowed.
     *
     * @var array
     */
    private static $POSSIBLE_STATUSES = array('member', 'non member', 'honorary member', 'supportive member', 'praesidium');

	/**
     * The ID of this UnionStatus.
     *
     * @var int
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * The Person this UnionStatus describes.
     *
     * @var Person
     *
     * @Column(name="person")
     * @ManyToOne(targetEntity="Litus\Entity\Users\Academic", cascade={"all"}, fetch="LAZY", inversedBy="unionStatuses")
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
     * The academic year this status was valid in. The format is yyzz (i.e. 0910, 1112).
     *
     * @var string
     *
     * @Column(type="string", length="4")
     */
    private $year;

    public function __construct(Person $person, $status)
    {
        if(UnionStatus::isValidPerson($person))
            $this->person = $person;
        else
            throw new InvalidArgumentException('Invalid person: ' . $person);
        $this->setStatus($status);
        $this->year = AcademicYear::getShortAcademicYear();
    }

    /**
     * Returns the unique ID of this UnionStatus.
     *
     * @return int the ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the Person.
     *
     * @return Person the person this UnionStatus belongs to.
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given person can have a UnionStatus.
     *
     * @static
     * @param Person $person the person to check
     * @return bool
     */
    public static function isValidPerson(Person $person)
    {
        return ($person != null) && $person->canHaveUnionStatus();
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
        if($this->isValidStatus($status))
            $this->status = $status;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param $status string a status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return (array_search($status, UnionStatus::$POSSIBLE_STATUSES, true) != false);
    }

    /**
     * Returns the academic year of this UnionStatus.
     *
     * @return string
     * @see \Litus\Util\AcademicYear::getShortAcademicYear(new DateTime('now'))
     */
    public function getYear()
    {
        return $this->year;
    }
}
