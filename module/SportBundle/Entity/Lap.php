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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    SportBundle\Entity\Department;

/**
 * This entity represents a lap.
 *
 * @ORM\Entity(repositoryClass="SportBundle\Repository\Lap")
 * @ORM\Table(name="sport.laps")
 */
class Lap
{
    /**
     * @var int The ID of this lap
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var AcademicYear The year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var Runner The person who ran this lap
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Runner", cascade={"persist"})
     * @ORM\JoinColumn(name="runner", referencedColumnName="id")
     */
    private $runner;

    /**
     * @var DateTime The time when this runner registered for this lap
     *
     * @ORM\Column(name="registration_time", type="datetime")
     */
    private $registrationTime;

    /**
     * @var DateTime|null The time this runner started his lap
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var DateTime|null The time this runner ended his lap
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Department The lap's department
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Department", inversedBy="members")
     * @ORM\JoinColumn(name="department", referencedColumnName="id")
     */
    private $department;

    /**
     * @param AcademicYear $academicYear
     * @param Runner       $runner
     */
    public function __construct(AcademicYear $academicYear, Runner $runner, Department $department = null)
    {
        $this->academicYear = $academicYear;

        $this->runner = $runner;
        $this->registrationTime = new DateTime();
        $this->department = $department;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param  Runner $runner
     * @return self
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param  Department $department
     * @return self
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationTime()
    {
        return $this->registrationTime;
    }

    /**
     * @return DateTime|null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Starts this lap.
     *
     * @return self
     */
    public function start()
    {
        $this->startTime = new DateTime();

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Ends this lap.
     *
     * @return self
     */
    public function stop()
    {
        $this->endTime = new DateTime();

        return $this;
    }

    /**
     * Returns the duration of the lap.
     *
     * @return DateInterval
     */
    public function getLapTime()
    {
        if (null === $this->startTime) {
            return new DateInterval('PT0S');
        }

        if (null !== $this->endTime) {
            $lapTime = $this->endTime->diff($this->startTime);
        } else {
            $now = new DateTime();
            $lapTime = $now->diff($this->startTime);
        }

        return $lapTime;
    }

    /**
     * Determines the number of points this lap is worth.
     *
     * @return integer
     */
    public function getPoints()
    {
        $pointsCriteria = unserialize(
            $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.points_criteria')
        );

        $seconds = $this->convertDateIntervalToSeconds($this->getLapTime());

        foreach ($pointsCriteria as $i => $pointsCriterium) {
            if (isset($pointsCriteria[$i + 1])) {
                if ($seconds > $pointsCriteria[$i + 1]['limit'] && $seconds <= $pointsCriterium['limit']) {
                    return $pointsCriterium['points'];
                }
            } else {
                if ($seconds <= $pointsCriterium['limit']) {
                    return $pointsCriterium['points'];
                }
            }
        }

        return 0;
    }

    /**
     * Converts a DateInterval to seconds.
     *
     * @param  DateInterval $interval The interval that should be converted
     * @return integer
     */
    private function convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }
}
