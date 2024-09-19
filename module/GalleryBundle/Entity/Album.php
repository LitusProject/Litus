<?php

namespace GalleryBundle\Entity;

use CommonBundle\Component\Util\Url as UrlUtil;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GalleryBundle\Entity\Album\Photo;
use GalleryBundle\Entity\Album\Translation;
use Locale;

/**
 * This entity stores the album item.
 *
 * @ORM\Entity(repositoryClass="GalleryBundle\Repository\Album")
 * @ORM\Table(name="gallery_albums")
 */
class Album
{
    /**
     * @var integer The ID of this album
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time of creation of this album
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var Person The person who created this album
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="create_person", referencedColumnName="id")
     */
    private $createPerson;

    /**
     * @var DateTime The date the photo's of this album were created
     *
     * @ORM\Column(name="date_activity", type="datetime")
     */
    private $dateActivity;

    /**
     * @var ArrayCollection The translations of this album
     *
     * @ORM\OneToMany(targetEntity="GalleryBundle\Entity\Album\Translation", mappedBy="album", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var string The name of this album
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var boolean Flag whether the photo's will have a watermark or not
     *
     * @ORM\Column(type="boolean")
     */
    private $watermark;

    /**
     * @var ArrayCollection The photos of this album
     *
     * @ORM\OneToMany(targetEntity="GalleryBundle\Entity\Album\Photo", mappedBy="album", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $photos;

    /**
     * @param Person $person
     */

    /**
     * @var string The poster of this event
     *
     * @ORM\Column(name="poster", type="string", nullable=true)
     */
    private $poster;

    public function __construct(Person $person)
    {
        $this->createTime = new DateTime();
        $this->createPerson = $person;

        $this->translations = new ArrayCollection();
        $this->photos = new ArrayCollection();
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
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @return Person
     */
    public function getCreatePerson()
    {
        return $this->createPerson;
    }

    /**
     * @param  DateTime $date
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->dateActivity = $date;

        if ($this->name === null) {
            $this->name = $date->format('d_m_Y_H_i');
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->dateActivity;
    }

    /**
     * @param  Translation $translation
     * @return self
     */
    public function addTranslation(Translation $translation)
    {
        $existing = $this->getTranslation($translation->getLanguage(), false);
        if ($existing !== null) {
            $this->removeTranslation($existing);
        }

        $this->translations->add($translation);

        return $this;
    }

    /**
     * @param  Translation $translation
     * @return self
     */
    public function removeTranslation(Translation $translation)
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback && isset($fallbackTranslation)) {
            return $fallbackTranslation;
        }

        return null;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getTitle();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $watermark
     *
     * @return self
     */
    public function setWatermark($watermark)
    {
        $this->watermark = $watermark;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasWatermark()
    {
        return $this->watermark;
    }

    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return Photo
     */
    public function getPhoto()
    {
        do {
            $num = rand(0, count($this->photos) - 1);
        } while (isset($this->photos[$num]) && $this->photos[$num]->isCensored());

        return $this->photos[$num];
    }

    /**
     *
     * @return self
     */
    public function updateName()
    {
        $translation = $this->getTranslation();
        $this->name = $this->getDate()->format('d_m_Y_H_i_s') . '_' . UrlUtil::createSlug($translation->getTitle());

        return $this;
    }

    /**
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     *
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');

        return $this;
    }
}
