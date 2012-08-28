<?php

namespace GalleryBundle\Entity\Album;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="GalleryBundle\Repository\Album\Photo")
 * @ORM\Table(name="gallery.photo")
 */
class Photo
{
    /**
     * @var int The ID of this photo
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \GalleryBundle\Entity\Album\Album The album of this translation
     *
     * @ORM\ManyToOne(targetEntity="GalleryBundle\Entity\Album\Album", inversedBy="photos")
     * @ORM\JoinColumn(name="album", referencedColumnName="id")
     */
    private $album;

    /**
     * @var string The path of this photo
     *
     * @ORM\Column(type="string")
     */
    private $filePath;

    /**
     * @var boolean Whether the photo is censored
     *
     * @ORM\Column(type="boolean")
     */
    private $censored = false;

    /**
     * @param \GalleryBundle\Entity\Album\Album $album
     * @param string $filePath
     */
    public function __construct(Album $album, $filePath)
    {
        $this->album = $album;
        $this->filePath = $filePath;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GalleryBundle\Entity\Album\Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getThumbPath()
    {
        return '/thumbs' . $this->filePath;
    }

    /**
     * @return boolean
     */
    public function isCensored()
    {
        return $this->censored;
    }

    /**
     * @param boolean $censored
     *
     * @return \GalleryBundle\Entity\Album\Photo
     */
    public function setCensored($censored)
    {
        $this->censored = $censored;
        return $this;
    }
}
