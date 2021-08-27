<?php

namespace CommonBundle\Entity\User\Status;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * A classification of a user based on his status at our Alma Mater.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Status\University")
 * @ORM\Table(name="users_statuses_university")
 */
class University
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    public static $possibleStatuses = array(
        'alumnus'                  => 'Alumnus',
        'assistant_professor'      => 'Assistant Professor',
        'administrative_assistant' => 'Administrative Assistant',
        'external_student'         => 'External Student',
        'professor'                => 'Professor',
        'student'                  => 'Student',
    );

    /**
     * @var integer The ID of this university status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person this university status belongs to
     *
     * @ORM\ManyToOne(
     *      targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="universityStatuses"
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
     * @param  Academic     $person       The person that should be given the status
     * @param  string       $status       The status that should be given to the person
     * @param  AcademicYear $academicYear The year of the status
     * @throws InvalidArgumentException
     */
    public function __construct(Academic $person, $status, AcademicYear $academicYear)
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
     * @return Academic
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a university status.
     *
     * @static
     * @param  Academic     $person       the user to check
     * @param  AcademicYear $academicYear The year of the status
     * @return boolean
     */
    public static function isValidPerson(Academic $person, AcademicYear $academicYear)
    {
        return ($person != null) && $person->canHaveUniversityStatus($academicYear);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param  string $status string the status to set
     * @return University
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
