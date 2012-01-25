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
 
namespace CommonBundle\Entity\Users;

use CommonBundle\Component\Util\AcademicYear,
	CommonBundle\Entity\Users\People\Academic;

/**
 * A classification of a user based on his status at our Alma Mater.
 * 
 * @Entity(repositoryClass="CommonBundle\Repository\Users\UniversityStatus")
 * @Table(name="users.university_statuses")
 */
class UniversityStatus
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    private static $_possibleStatuses = array(
        'professor', 'student', 'alumnus', 'external_student'
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
     * @var \CommonBundle\Entity\Users\People\Academic The Person this university status belongs to
     *
     * @Column(name="person")
     * @ManyToOne(
     *      targetEntity="CommonBundle\Entity\Users\People\Academic", inversedBy="universityStatuses"
     * )
     */
    private $person;

    /**
     * @var string The actual status value
     *
     * @Column(type="string")
     */
    private $status;

    /**
     * @var string The academic year this status is/was valid in; the format is yyzz (i.e. 0910, 1112)
     *
     * @Column(type="string", length=4)
     */
    private $year;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $person The person that should be given the status
     * @param string $status The status that should be given to the person
     * @throws \InvalidArgumentException
     */
    public function __construct(Academic $person, $status)
    {
        if (!UniversityStatus::isValidPerson($person))
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
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a UniversityStatus.
     *
     * @static
     * @param \CommonBundle\Entity\Users\People\Academic $person the user to check
     * @return bool
     */
    public static function isValidPerson(Academic $person)
    {
        return ($person != null) && $person->canHaveUniversityStatus();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status string the status to set
     * @return \CommonBundle\Entity\Users\UniversityStatus;
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
        return in_array($status, self::$_possibleStatuses);
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
}
