<?php

namespace CommonBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Node")
 * @ORM\Table(name="nodes_nodes")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "banner"="BannerBundle\Entity\Node\Banner",
 *      "form"="FormBundle\Entity\Node\Form",
 *      "form_group"="FormBundle\Entity\Node\Group",
 *      "page"="PageBundle\Entity\Node\Page",
 *      "faq"="CommonBundle\Entity\General\Node\FAQ\FAQ",
 *      "news"="NewsBundle\Entity\Node\News",
 *      "notification"="NotificationBundle\Entity\Node\Notification",
 *      "event"="CalendarBundle\Entity\Node\Event"
 * })
 */
abstract class Node
{
    /**
     * @var integer The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var Person The person who created this node
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @param Person $creationPerson
     */
    public function __construct(Person $creationPerson)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $creationPerson;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }
}
