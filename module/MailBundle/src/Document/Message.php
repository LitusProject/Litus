<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace MailBundle\Document;

use DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * This entity stores an e-mail message, received through our own
 * mail parser.
 *
 * @ODM\Document(
 *     collection="mailbundle_messages",
 *     repositoryClass="MailBundle\Repository\Message"
 * )
 */
class Message
{
    /**
     * @var integer The ID of this message
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The time of creation of this message
     *
     * @ODM\Field(name="creation_time", type="date")
     */
    private $creationTime;

    /**
     * @var string The subject of this message
     *
     * @ODM\Field(type="string")
     */
    private $subject;

    /**
     * @var string The message string
     *
     * @ODM\Field(type="string")
     */
    private $message;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The attachemnts for this message
     *
     * @ODM\ReferenceMany(targetDocument="MailBundle\Document\Messages\Attachment", cascade={"persist", "remove"})
     */
    private $attachments;

    /**
     * @param string $message
     * @param array $attachments
     */
    public function __construct($subject, $message, array $attachments = array())
    {
        $this->creationTime = new DateTime();
        
        $this->subject = $subject;
        $this->message = $message;
        $this->attachments = new ArrayCollection($attachments);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return \MailBundle\Document\Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return \MailBundle\Document\Message
     */
    public function setMessage($message)
    {
        $this->message = $message;
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
     * @param array $attachments
     * @return \MailBundle\Document\Message
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = new ArrayCollection($attachments);
        return $this;
    }
}
