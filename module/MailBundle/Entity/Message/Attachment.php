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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\Message;

/**
 * This entity stores an attachment from an e-mail message.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Message\Attachment")
 * @ORM\Table(name="mail_messages_attachments")
 */
class Attachment
{
    /**
     * @var integer The ID of this attachment
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Message The message this attachment belongs to
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\Message", inversedBy="attachments")
     * @ORM\JoinColumn(name="message", referencedColumnName="id")
     */
    private $message;

    /**
     * @var string The filename of this attachment
     *
     * @ORM\Column(type="string")
     */
    private $filename;

    /**
     * @var string The attachment's content type
     *
     * @ORM\Column(name="content_type", type="string")
     */
    private $contentType;

    /**
     * @var string The attachment's binary data
     *
     * @ORM\Column(type="blob")
     */
    private $data;

    /**
     * @param Message $message
     * @param string  $filename
     * @param string  $contentType
     * @param string  $data
     */
    public function __construct($message, $filename, $contentType, $data)
    {
        $this->message = $message;
        $this->filename = $filename;
        $this->contentType = $contentType;
        $this->data = $data;
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
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
