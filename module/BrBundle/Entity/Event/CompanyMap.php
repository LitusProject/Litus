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

namespace BrBundle\Entity\Event;

use BrBundle\Entity\Company;
use BrBundle\Entity\Event;
use BrBundle\Entity\Event\CompanyMetadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\CompanyMap")
 * @ORM\Table(name="br_events_companies_map")
 */
class CompanyMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *@var Company The company that will be attending this event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $company;

    /**
     *@var Event The event that the company will be attending
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $event;

    /**
     * @var CompanyAttendee All the attendees that will be attending for this company
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Event\CompanyAttendee", mappedBy="companyMap")
     */
    private $attendees;

    /**
     *@var CompanyMetadata The metadata of the company for this event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event\CompanyMetadata")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $metadata;

    /**
     * @param Company $company
     * @param Event   $event
     */
    public function __construct(Company $company, Event $event)
    {
        $this->company = $company;
        $this->event = $event;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return CompanyAttendee
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param CompanyAttendee $attendees
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
    }

    /**
     * @param  CompanyMetadata $metadata
     *
     * @return self
     */
    public function setCompanyMetadata(CompanyMetadata $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return CompanyMetadata
     */
    public function getCompanyMetadata()
    {
        return $this->metadata;
    }

}
