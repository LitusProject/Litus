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

namespace FormBundle\Entity\Mail;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Mail\Translation")
 * @ORM\Table(name="forms.mails_translations")
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
     * @var \FormBundle\Entity\Mail\Mail The mail of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Mail\Mail", inversedBy="translations")
     * @ORM\JoinColumn(name="mail", referencedColumnName="id")
     */
    private $mail;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
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
     * @param \FormBundle\Entity\Mail\Mail          $mail
     * @param \CommonBundle\Entity\General\Language $language
     * @param string                                $subject
     * @param string                                $content
     */
    public function __construct(Mail $mail, Language $language, $subject, $content)
    {
        $this->mail = $mail;
        $this->language = $language;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * @return \FormBundle\Entity\Mail\Mail
     */
    public function getMail()
    {
        return $this->mail;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return \FormBundle\Entity\Mail\Translation
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
     * @param string $content
     *
     * @return \FormBundle\Entity\Mail\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
