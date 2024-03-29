<?php

namespace FormBundle\Entity\Mail;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Mail;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Mail\Translation")
 * @ORM\Table(name="form_mails_translations")
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
     * @var Mail The mail of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Mail", inversedBy="translations")
     * @ORM\JoinColumn(name="mail", referencedColumnName="id")
     */
    private $mail;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The subject of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $subject;

    /**
     * @var string The content of this tanslation
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param Mail     $mail
     * @param Language $language
     * @param string   $subject
     * @param string   $content
     */
    public function __construct(Mail $mail, Language $language, $subject, $content)
    {
        $this->mail = $mail;
        $this->language = $language;
        $this->subject = $subject;
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
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param  string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
