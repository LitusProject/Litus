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

namespace ShiftBundle\Entity;

/**
 * This entity stores a shift.
 *
 * @Entity(repositoryClass="ShiftBundle\Repository\Shift")
 * @Table(name="shifts.shifts")
 */
class Shift
{
    /**
     * @var integer The ID of this shift
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var boolean The moment this shift starts
     *
     * @Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var string The moment this shift ends
     *
     * @Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var \CommonBundle\Entity\Users\Person The person that manages this shift
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     */
    private $manager;

    /**
     * @var integer The required number of responsibles for this shift
     *
     * @Column(type="integer")
     */
    private $nbResponsibles;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The people that are responsible for this shift
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinTable(
     *      name="shifts.shifts_responsibles_map",
     *      joinColumns={@JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
     */
    private $responsibles;

    /**
     * @var integer The required number of volunteers for this shift
     *
     * @Column(name="nb_volunteers", type="integer")
     */
    private $nbVolunteers;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The people that volunteered for this shift
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinTable(
     *      name="shifts.shifts_volunteers_map",
     *      joinColumns={@JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
     */
    private $volunteers;

    /**
     * @var \ShiftBundle\Entity\Unit The organization unit this shift belongs to
     *
     * @ManyToOne(targetEntity="ShiftBundle\Entity\Unit")
     */
    private $unit;

    /**
     * @var [type]
     *
     * @ManyToOne(targetEntity="CalendarBundle\Entity\Nodes\Event")
     */
    private $event;

    public function __construct(DateTime $startDate, DateTime $endDate, Person $manager, $nbResponsibles, $nbVolunteers, Unit $unit, Event $event)
    {
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \ShiftBundle\Entity\Unit
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
