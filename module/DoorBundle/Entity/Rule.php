<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace DoorBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an access rule for our door.
 *
 * @ORM\Entity(repositoryClass="DoorBundle\Repository\Rule")
 * @ORM\Table(name="door_rules")
 */
class Rule
{
    /**
     * @var integer The ID of this rule
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The ID of the academic
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var DateTime The start date of the rule
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date of the rule
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var integer The time from when access is allowed
     *
     * @ORM\Column(name="start_time", type="integer")
     */
    private $startTime;

    /**
     * @var integer The time until when access is allowed
     *
     * @ORM\Column(name="end_time", type="integer")
     */
    private $endTime;

    /**
     * @param Academic $academic
     */
    public function __construct(Academic $academic)
    {
        $this->academic = $academic;
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
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getStartTimeReadable()
    {
        return self::intToTime($this->startTime);
    }

    /**
     * @param  integer $startTime
     * @return self
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return integer
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return string
     */
    public function getEndTimeReadable()
    {
        return self::intToTime($this->endTime);
    }

    /**
     * @param  integer $endTime
     * @return self
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Prints an integer time as hh:mm
     *
     * @param  integer|null $time
     * @return string
     */
    private static function intToTime($time)
    {
        $hour = floor($time / 100);
        $mins = $time % 100;

        if ($mins < 10) {
            $mins = '0' . $mins;
        }

        if ($hour < 10) {
            // jQuery timepicker needs hh:mm
            $hour = '0' . $hour;
        }

        return $hour . ':' . $mins;
    }
}
