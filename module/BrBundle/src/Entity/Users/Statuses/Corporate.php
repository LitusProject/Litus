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

namespace BrBundle\Entity\Users\Statuses;

use BrBundle\Entity\Users\People\Corporate as CorporatePerson,
    CommonBundle\Component\Util\AcademicYear,
    Doctrine\ORM\Mapping as ORM;

/**
 * A classification of a user based on his status at our Alma Mater.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Users\Statuses\Corporate")
 * @ORM\Table(name="users.corporate_statuses")
 */
class Corporate
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    private static $_possibleStatuses = array(
        'correspondence', 'signatory'
    );

    /**
     * @var int The ID of this corporate status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Users\People\Corporate The person this company status belongs to
     *
     * @ORM\ManyToOne(
     *      targetEntity="BrBundle\Entity\Users\People\Corporate", inversedBy="corporateStatuses"
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
     * @var string The academic year this status was assigned; the format is yyzz (i.e. 0910, 1112)
     *
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @param \BrBundle\Entity\Users\People\Corporate $person The person that should be given the status
     * @param string $status The status that should be given to the person
     * @throws \InvalidArgumentException
     */
    public function __construct(CorporatePerson $person, $status)
    {
        if (!self::isValidPerson($person))
            throw new \InvalidArgumentException('Invalid person');

        $this->person = $person;

        $this->setStatus($status);
        $this->year = AcademicYear::getShortAcademicYear();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Users\People\Corporate
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a corporate status.
     *
     * @static
     * @param \BrBundle\Entity\Users\People\Corporate $person the user to check
     * @return bool
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
