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
     * @ORM\JoinColumn(referencedColumnName="id")
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
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $event;

    /**
     * @param Company $company
     * @param Event   $event
     */
    public function __construct(Company $company, Event $event)
    {
        $this->company = $company;
        $this->event = $event;
        $this->done = false;
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
}
