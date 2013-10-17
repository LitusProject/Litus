<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

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
     * @var \CommonBundle\Entity\General\AcademicYear The year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \SportBundle\Entity\Runner The person who ran this lap
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Runner", cascade={"persist"})
     * @ORM\JoinColumn(name="runner", referencedColumnName="id")
     */
    private $runner;

    /**
     * @var \DateTime The time when this runner registered for this lap
     *
     * @ORM\Column(name="registration_time", type="datetime")
     */
    private $registrationTime;

    /**
     * @var \DateTime The time this runner started his lap
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime The time this runner ended his lap
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \SportBundle\Entity\Runner $runner
     */
    public function __construct(AcademicYear $academicYear, Runner $runner)
    {
        $this->academicYear = $academicYear;

        $this->runner = $runner;
        $this->registrationTime = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \SportBundle\Entity\Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param \SportBundle\Entity\Runner $runner
     * @return \SportBundle\Entity\Lap
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationTime()
    {
        return $this->registrationTime;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Starts this lap.
     *
     * @return \SportBundle\Entity\Lap
     */
    public function start()
    {
        $this->startTime = new DateTime();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Ends this lap.
     *
     * @return \SportBundle\Entity\Lap
     */
    public function stop()
    {
        $this->endTime = new DateTime();
        return $this;
    }

    /**
     * Returns the duration of the lap.
     *
     * @return \DateInterval
     */
    public function getLapTime()
    {
        if (null !== $this->endTime) {
            $lapTime = $this->endTime->diff($this->startTime);
        } else {
            $now = new \DateTime();
            $lapTime = $now->diff($this->startTime);
        }

        return $lapTime;
    }

    /**
     * Determines the number of points this lap is worth.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @return integer
     */
    public function getPoints(EntityManager $entityManager)
    {
        $pointsCriteria = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.points_criteria')
        );

        $points = 0;
        foreach ($pointsCriteria as $i => $limit) {
            if (isset($pointsCriteria[$i+1])) {
                if (
                    $this->getLapTime() < new DateInterval('PT' . $limit . 'S')
                    && $this->getLapTime() >= new DateInterval('PT' . $pointsCriteria[$i+1] . 'S')
                )
                    $points = $i+1;
            } else {
                if ($this->getLapTime() < new DateInterval('PT' . $limit . 'S'))
                    $points = $i+1;
            }
        }

        return $points;
    }
}
