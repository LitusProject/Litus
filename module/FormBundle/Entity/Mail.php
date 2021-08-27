<?php

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Mail")
 * @ORM\Table(name="form_mails")
 */
class Mail
{
    /**
     * @var integer The mail unique identifier
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Mail\Translation", mappedBy="mail", cascade={"remove"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getSubject(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getSubject();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getContent();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return \FormBundle\Entity\Mail\Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        if (count($this->translations) == 0) {
            return null;
        }

        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language && strlen($translation->getSubject()) > 0) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback && isset($fallbackTranslation)) {
            return $fallbackTranslation;
        }

        return null;
    }
}
