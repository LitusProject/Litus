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
 
namespace CommonBundle\Entity\Users\Statuses;

use CommonBundle\Entity\General\AcademicYear,
	CommonBundle\Entity\Users\Person;

/**
 * Specifying the different types of memberships the organization has.
 * 
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Statuses\Organisation")
 * @Table(name="users.organization_statuses")
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
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * @var \CommonBundle\Entity\Users\Person The person this union status describes
     *
     * @ManyToOne(
     * 		targetEntity="CommonBundle\Entity\Users\Person", inversedBy="unionStatuses"
     * )
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The actual status value
     *
     * @Column(type="string")
     */
    private $status;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the status
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param \CommonBundle\Entity\Users\Person $person The person this union status describes
     * @param string $status The actual status value
     * @throws \InvalidArgumentException
     */
    public function __construct(Person $person, $status, AcademicYear $academicYear)
    {
        if(!self::isValidPerson($person))
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
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given person can have a UnionStatus.
     *
     * @static
     * @param \CommonBundle\Entity\Users\Person $person The person to check
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
     * @return \CommonBundle\Entity\Users\UnionStatus
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
     * @return string
     */
    public function getYear()
    {
        return $this->academicYear->getCode(true);
    }
}
