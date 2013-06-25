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

namespace NotificationBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\Nodes\Notification")
 * @ORM\Table(name="nodes.notifications")
 */
class Notification extends \CommonBundle\Entity\Node
{

    /**
     * @var The reservation's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var string The title of this notification
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var boolean The flag whether the notification is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param string $category
     */
    public function __construct(Person $person, $content, DateTime $startDate, DateTime $endDate, $active )
    {
        parent::__construct($person);

        $this->content = $content;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
    }

    /**
     * @param string $content
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param DateTime $startDate
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param boolean $active
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive() {
        return $this->active;
    }
}
