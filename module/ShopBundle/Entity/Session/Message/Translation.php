<?php

namespace ShopBundle\Entity\Session\Message;

use CommonBundle\Entity\General\Language;
use ShopBundle\Entity\Session\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Session\Message\Translation")
 * @ORM\Table(name="shop_sessions_messages_translations")
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
     * @var Message The message of this translation
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Session\Message", inversedBy="translations")
     * @ORM\JoinColumn(name="message", referencedColumnName="id")
     */
    private $message;

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
     * @ORM\Column(type="string")
     */
    private $content;

    /**
     * @param Message $message
     * @param Language    $language
     * @param string      $content
     */
    public function __construct(Message $message, Language $language, $content)
    {
        $this->message = $message;
        $this->language = $language;
        $this->content = $content;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
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
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}