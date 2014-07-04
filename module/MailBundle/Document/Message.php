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
     * @var DateTime The time of creation of this message
     *
     * @ODM\Field(name="creation_time", type="date")
     */
    private $creationTime;

    /**
     * @var string The type of this message
     *
     * @ODM\Field(type="string")
     */
    private $type;

    /**
     * @var string The subject of this message
     *
     * @ODM\Field(type="string")
     */
    private $subject;

    /**
     * @var string The body string
     *
     * @ODM\Field(type="string")
     */
    private $body;

    /**
     * @var ArrayCollection The attachemnts for this message
     *
     * @ODM\ReferenceMany(targetDocument="MailBundle\Document\Message\Attachment", cascade={"persist", "remove"})
     */
    private $attachments;

    /**
     * @param string $type
     * @param string $subject
     * @param string $body
     * @param array  $attachments
     */
    public function __construct($type, $subject, $body, array $attachments = array())
    {
        $this->creationTime = new DateTime();

        $this->type = $type;
        $this->attachments = new ArrayCollection($attachments);

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
        $this->body = utf8_encode(str_replace(chr( 194 ) . chr( 160 ), " ", $body));

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
