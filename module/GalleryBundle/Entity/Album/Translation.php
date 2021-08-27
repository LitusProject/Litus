<?php

namespace GalleryBundle\Entity\Album;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use GalleryBundle\Entity\Album;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="GalleryBundle\Repository\Album\Translation")
 * @ORM\Table(name="gallery_albums_translations")
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
     * @var Album The album of this translation
     *
     * @ORM\ManyToOne(targetEntity="GalleryBundle\Entity\Album", inversedBy="translations")
     * @ORM\JoinColumn(name="album", referencedColumnName="id")
     */
    private $album;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The title of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @param Album    $album
     * @param Language $language
     * @param string   $title
     */
    public function __construct(Album $album, Language $language, $title)
    {
        $this->album = $album;
        $this->language = $language;
        $this->title = $title;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Album
     */
    public function getAlbum()
    {
        return $this->album;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
