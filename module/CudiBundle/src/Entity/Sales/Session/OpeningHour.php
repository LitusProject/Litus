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

namespace CudiBundle\Entity\Sales\Session;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Session\OpeningHour")
 * @ORM\Table(name="cudi.sales_session_openinghour")
 */
class OpeningHour
{
    /**
     * @var integer The ID of the openinghour
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The start time of this period
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end time of this period
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var \CommonBundle\Entity\Users\Person The person who created this entity
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \DateTime The time this entity was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(DateTime $startDate, DateTime $endDate, Person $person)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->person = $person;
        $this->timestamp = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return \CudiBundle\Entity\Sales\Session\OpeningHour
     */
    public function setStart(DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return \CudiBundle\Entity\Sales\Session\OpeningHour
     */
    public function setEnd(DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}