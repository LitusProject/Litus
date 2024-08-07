<?php

namespace BrBundle\Entity\User\Status;

use BrBundle\Entity\User\Person\Corporate as CorporatePerson;
use CommonBundle\Component\Util\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * A classification of a user based on his status at our Alma Mater.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\User\Status\Corporate")
 * @ORM\Table(name="users_statuses_corporate")
 */
class Corporate
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    private static $possibleStatuses = array(
        'correspondence',
        'signatory',
    );

    /**
     * @var integer The ID of this corporate status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CorporatePerson The person this company status belongs to
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate", inversedBy="corporateStatuses")
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
     * @var string The academic year this status was assigned; the format is yyzz (i.e. 0910, 1112)
     *
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @param  CorporatePerson $person The person that should be given the status
     * @param  string          $status The status that should be given to the person
     * @throws InvalidArgumentException
     */
    public function __construct(CorporatePerson $person, $status)
    {
        if (!self::isValidPerson($person)) {
            throw new InvalidArgumentException('Invalid person');
        }

        $this->person = $person;

        $this->setStatus($status);
        $this->year = AcademicYear::getShortAcademicYear();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CorporatePerson
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a corporate status.
     *
     * @static
     * @param  CorporatePerson $person the user to check
     * @return boolean
     */
    public static function isValidPerson(CorporatePerson $person)
    {
        return ($person !== null) && $person->canHaveCorporateStatus();
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
     * @return Corporate
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
        return in_array($status, self::$possibleStatuses);
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
}
