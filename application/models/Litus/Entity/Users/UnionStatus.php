<?php

namespace Litus\Entity\Users;

use \Litus\Entity\Users\Person;
use \Litus\Util\AcademicYear;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\UnionStatus")
 * @Table(name="users.union_statuses")
 */
class UnionStatus
{
    /**
     * @var array All the possible status values allowed
     */
    private static $POSSIBLE_STATUSES = array('member', 'non member', 'honorary member', 'supportive member', 'praesidium');

	/**
     * @var int The ID of this union status
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * @var \Litus\Entity\Users\Person The person this union status describes
     *
     * @Column(name="person")
     * @ManyToOne(targetEntity="Litus\Entity\Users\Academic", inversedBy="unionStatuses")
     */
    private $person;

    /**
     * @var string The actual status value
     *
     * @Column(type="string")
     */
    private $status;

    /**
     * @var string The academic year this status was valid in; the format is yyzz (i.e. 0910, 1112)
     *
     * @Column(type="string", length=4)
     */
    private $year;

    /**
     * @throws \InvalidArgumentException
     * @param \Litus\Entity\Users\Person $person The person this union status describes
     * @param string $status The actual status value
     */
    public function __construct(Person $person, $status)
    {
        if(!UnionStatus::isValidPerson($person))
            throw new \InvalidArgumentException('Invalid person');
        $this->person = $person;
        
        $this->setStatus($status);
        $this->year = AcademicYear::getShortAcademicYear();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Litus\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given person can have a UnionStatus.
     *
     * @static
     * @param \Litus\Entity\Users\Person $person The person to check
     * @return bool
     */
    public static function isValidPerson(Person $person)
    {
        return ($person != null) && $person->canHaveUnionStatus();
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
     * @return Litus\Entity\Users\UnionStatus
     */
    public function setStatus($status)
    {
        if(self::isValidStatus($status))
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
        return in_array($status, UnionStatus::$POSSIBLE_STATUSES);
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
}
