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
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $company;

    /**
     * @var boolean Whether the contract has been signed
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $done;

    /**
     *@var Event The event that the company will be attending
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var integer Number of attendees that will be attending for this company
     *
     * @ORM\Column(type="bigint", options={"default" = 0})
     */
    private $attendees;
    
    /**
     * @var string The master interests of the company
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $masterInterests;

    /**
     * @var string The notes about the attending company
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @var boolean Whether the information has been checked
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $checked;

    /**
     * @param Company $company
     * @param Event   $event
     */
    public function __construct(Company $company, Event $event)
    {
        $this->company = $company;
        $this->event = $event;
        $this->done = false;
        $this->attendees = 0;
        $this->checked = false;
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
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param integer $attendees
     * @return self
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
        return $this;
    }

    /**
     * @return self
     */
    public function setDone()
    {
        $this->done = true;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @return  string The status of the contract for this
     */
    public function getStatus()
    {
        if ($this->done === true) {
            return 'Done';
        }
        return 'In Progress';
    }

    /**
     * @return array $masterInterests
     */
    public function getMasterInterests()
    {
        if (is_string($this->masterInterests) && substr($this->masterInterests, 0, 2) != 'a:') {
            throw new \RuntimeException('Badly formatted master interests in company metadata (get)');
        }
        return unserialize($this->masterInterests);
    }

    /**
     * @param  array $master_interests
     * @return self
     */
    public function setMasterInterests($masterInterests)
    {
        $this->masterInterests = serialize($masterInterests);

        return $this;
    }

    /**
     * @param  string $notes
     * @return
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return  string The notes of the company
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param  boolean $checked
     * @return self
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isChecked()
    {
        return $this->checked ? $this->checked : false;
    }
}
