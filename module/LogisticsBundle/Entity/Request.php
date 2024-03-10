<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a request for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Request")
 * @ORM\Table(name="logistics_request")
 */
class Request
{
    /**
     * @var integer request's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var boolean True if the request has been handled, false if not.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $handled;

    /**
     * @var boolean True if the request has been removed, false if not.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $removed;

    /**
     * @var boolean True if the request has been removed, false if not.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $canceled;

    /**
     * @var Academic The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private Academic $contact;

    /**
     * //     * @param Academic $contact
     */
    public function __construct(Academic $contact)
    {
        $this->creationTime = new DateTime();
        $this->handled = false;
        $this->removed = false;
        $this->canceled = false;
        $this->contact = $contact;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isHandled()
    {
        return $this->handled;
    }

    /**
     * @param boolean $handled
     */
    public function setHandled(bool $handled)
    {
        $this->handled = $handled;
    }

    /**
     * @return boolean
     */
    public function handled()
    {
        $this->handled = true;

        return $this->handled;
    }

    /**
     * @return boolean
     */
    public function isCanceled()
    {
        return $this->canceled;
    }

    /**
     * @param boolean $canceled
     */
    public function setCanceled(bool $canceled)
    {
        $this->removed = $canceled;
    }

    /**
     * @return boolean
     */
    public function cancel()
    {
        $this->canceled = true;

        return $this->canceled;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @param boolean $removed
     */
    public function setRemoved(bool $removed)
    {
        $this->removed = $removed;
    }

    /**
     * @return boolean
     */
    public function remove()
    {
        $this->canceled = false;
        $this->removed = true;

        return $this->removed;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @param Academic $contact
     * @return Request
     */
    public function setContact(Academic $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Academic
     */
    public function getContact(): Academic
    {
        return $this->contact;
    }
}
