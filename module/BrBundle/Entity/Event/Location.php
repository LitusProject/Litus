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
use RuntimeException;

/**
 * Location
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\Location")
 * @ORM\Table(name="br_events_companies_location", uniqueConstraints={@ORM\UniqueConstraint(name="number_event_unique",columns={"event_entity", "number"})})
 */
class Location
{
    /**
     * @var integer The ID of the location
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
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $company;


    /**
     *@var Event The company that will be attending this event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(name="event_entity", referencedColumnName="id")
     */
    private $event;
    

    /**
     * @var integer The number of the location
     *
     * @ORM\Column(type="bigint")
     */
    private $number;


    /**
     * @var integer x position of the location
     *
     * @ORM\Column(type="bigint")
     */
    private $x;


    /**
     * @var integer y position of the location
     *
     * @ORM\Column(type="bigint")
     */
    private $y;


    /**
     * @var string orientation of the location
     *
     * @ORM\Column(name="orientation", type="text")
     *
     */
    private $orientation;


    /**
     * @var string Type of location
     *
     * @ORM\Column(name="type", type="text")
     *
     */
    private $type;


    const POSSIBLE_LOCATION_TYPES = array(
        'rectangle' => 'Rectangle',
        'circle'    => 'Circle',
    );


    const LOCATION_WIDTH = array(
        'rectangle' => 50,
        'circle'    => 38,
    );

    const LOCATION_HEIGHT = array(
        'rectangle' => 38,
        'circle'    => 38,
    );

    /**
     * @param Company $company
     * @param Event   $event
     */
    public function __construct()
    {}

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }


    /**
     * @param integer $number
     * @return None
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }


    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return None
     */
    public function setX($x)
    {
        $this->x = $x;
    }


    /**
     * @return integer
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return None
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return None
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return String orientation
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @return None
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return String orientation
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->LOCATION_HEIGHT[$this->type];
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->LOCATION_WIDTH[$this->type];
    }

}