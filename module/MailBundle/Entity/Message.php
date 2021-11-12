<?php

namespace MailBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores an e-mail message, received through our own mail parser.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Message")
 * @ORM\Table(name="mail_messages")
 */
class Message
{
    /**
     * @var integer The ID of this message
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time of creation of this message
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var string The type of this message
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The subject of this message
     *
     * @ORM\Column(type="string")
     */
    private $subject;

    /**
     * @var string The body string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var ArrayCollection The attachemnts for this message
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\Message\Attachment", mappedBy="message", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $attachments;

    /**
     * @param string $type
     * @param string $subject
     * @param string $body
     */
    public function __construct($type, $subject, $body)
    {
        $this->creationTime = new DateTime();

        $this->type = $type;
        $this->attachments = new ArrayCollection();

        $this->setSubject($subject);
        $this->setBody($body);
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
     * @param  DateTime $creationTime
     * @return self
     */
    public function setCreationTime(DateTime $creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return utf8_decode($this->subject);
    }

    /**
     * @param  string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = utf8_encode($subject);

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return utf8_decode($this->body);
    }

    /**
     * @param  string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = utf8_encode(str_replace(chr(194) . chr(160), ' ', $body));

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments->toArray();
    }

    /**
     * @param  array $attachments
     * @return self
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = new ArrayCollection($attachments);

        return $this;
    }
}
