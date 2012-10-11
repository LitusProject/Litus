<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

/**
 * @Entity(repositoryClass="SportBundle\Repository\Lap")
 * @Table(name="sport.laps")
 */
class Lap
{
    /**
     * @var int The ID of this lap
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var [type]
     */
    private $academicYear;

    /**
     * @var \Litus\Entity\Sport\Runner The person who ran this lap
     *
     * @ManyToOne(targetEntity="SportBundle\Entity\Runner", cascade={"persist"})
     * @JoinColumn(name="runner", referencedColumnName="university_identification")
     */
    private $runner;

    /**
     * @var \DateTime The time when this runner registered for this lap
     *
     * @Column(name="registration_time", type="datetime")
     */
    private $registrationTime;

    /**
     * @var \DateTime The time this runner started his lap
     *
     * @Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime The time this runner ended his lap
     *
     * @Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @param \Litus\Entity\Sport\Runner $runner The person who ran this lap
     */
    public function __construct(Runner $runner)
    {
        $this->setRunner($runner);

        $this->registrationTime = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Litus\Entity\Sport\Runner $runner The person who ran this lap
     * @return \Litus\Entity\Sport\Lap
     */
    public function setRunner(Runner $runner)
    {
        if (null === $runner)
            throw new \InvalidArgumentException('Invalid runner');
        $this->runner = $runner;

        return $this;
    }

    /**
     * @return \Litus\Entity\Sport\Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationTime()
    {
        return $this->registrationTime;
    }

    /**
     * Starts this lap
     *
     * @return \Litus\Entity\Sport\Lap
     */
    public function start()
    {
        $this->startTime = new \DateTime();
        return $this;
    }

    /**
     * Ends this lap
     *
     * @return \Litus\Entity\Sport\Lap
     */
    public function stop()
    {
        $this->endTime = new \DateTime();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
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
}
