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
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Mail\Mail")
 * @ORM\Table(name="forms.mails")
 */
class Mail
{
    /**
     * @var The mail unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The email address from which the mail is sent.
     *
     * @ORM\Column(name="mail_from", type="text")
     */
    private $from;

    /**
     * @var boolean Whether to send a copy to the sender or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $bcc;

    /**
     * @var \Doctrine\Common\Collection\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Mail\Translation", mappedBy="mail", cascade={"remove"})
     */
    private $translations;

    /**
     * @param string  $from
     * @param boolean $bcc
     */
    public function __construct($from, $bcc)
    {
        $this->from = $from;
        $this->bcc = $bcc;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $from
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param  boolean $bcc
     * @return self
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param  Language $language
     * @param  boolean  $allowFallback
     * @return string
     */
    public function getSubject(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getSubject();

        return '';
    }

    /**
     * @param  Language $language
     * @param  boolean  $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getContent();

        return '';
    }

    /**
     * @param  Language                                 $language
     * @param  boolean                                  $allowFallback
     * @return \FormBundle\Entity\Mail\Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        if (sizeof($this->translations) == 0)
            return null;

        foreach ($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language && strlen($translation->getSubject()) > 0)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }
}
