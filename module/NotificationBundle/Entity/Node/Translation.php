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

namespace NotificationBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\Node\Translation")
 * @ORM\Table(name="nodes.notification_translations")
 */
class Translation
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \NotificationBundle\Entity\Node\Notification The notification of this translation
     *
     * @ORM\ManyToOne(targetEntity="NotificationBundle\Entity\Node\Notification", inversedBy="translations")
     * @ORM\JoinColumn(name="notification", referencedColumnName="id")
     */
    private $notification;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this translation
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
     * @param \NotificationBundle\Entity\Node\Notification $notification
     * @param \CommonBundle\Entity\General\Language        $language
     * @param string                                       $content
     */
    public function __construct(Notification $notification, Language $language, $content)
    {
        $this->notification = $notification;
        $this->language = $language;
        $this->content = $content;
    }

    /**
     * @return \NotificationBundle\Entity\Node\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
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
     *
     * @param \NotificationBundle\Entity\Node\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
