<?php

namespace NotificationBundle\Entity\Node\Notification;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use NotificationBundle\Entity\Node\Notification;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\Node\Notification\Translation")
 * @ORM\Table(name="nodes_notifications_translations")
 */
class Translation
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Notification The notification of this translation
     *
     * @ORM\ManyToOne(targetEntity="NotificationBundle\Entity\Node\Notification", inversedBy="translations")
     * @ORM\JoinColumn(name="notification", referencedColumnName="id")
     */
    private $notification;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The content of this translation
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param Notification $notification
     * @param Language     $language
     * @param string       $content
     */
    public function __construct(Notification $notification, Language $language, $content)
    {
        $this->notification = $notification;
        $this->language = $language;
        $this->content = $content;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @param self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
