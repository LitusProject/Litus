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

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Option")
 * @ORM\Table(name="tickets.events_options")
 */
class Option
{
    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The event of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="tickets")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer The price for members
     *
     * @ORM\Column(name="price_members", type="smallint")
     */
    private $priceMembers;

    /**
     * @var integer The price for non members
     *
     * @ORM\Column(name="price_non_members", type="smallint")
     */
    private $priceNonMembers;

    /**
     * @param \TicketBundle\Entity\Event $event
     * @param string $name
     * @param integer $priceMembers
     * @param integer $priceNonMembers
     */
    public function __construct(Event $event, $name, $priceMembers, $priceNonMembers)
    {
        $this->event = $event;
        $this->name = $name;

        $this->setPriceMembers($priceMembers)
            ->setPriceNonMembers($priceNonMembers);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \TicketBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \TicketBunlde\Entity\Option
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriceMembers()
    {
        return $this->priceMembers;
    }

    /**
     * @param integer $priceMembers
     * @return \TicketBunlde\Entity\Option
     */
    public function setPriceMembers($priceMembers)
    {
        $this->priceMembers = $priceMembers * 100;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriceNonMembers()
    {
        return $this->priceNonMembers;
    }

    /**
     * @param integer $priceNonMembers
     * @return \TicketBunlde\Entity\Option
     */
    public function setPriceNonMembers($priceNonMembers)
    {
        $this->priceNonMembers = $priceNonMembers * 100;
        return $this;
    }
}
