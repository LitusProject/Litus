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

namespace GalleryBundle\Entity\Album;

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="GalleryBundle\Repository\Album\Translation")
 * @ORM\Table(name="gallery.translations")
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
     * @var \GalleryBundle\Entity\Album\Album The album of this translation
     *
     * @ORM\ManyToOne(targetEntity="GalleryBundle\Entity\Album\Album", inversedBy="translations")
     * @ORM\JoinColumn(name="album", referencedColumnName="id")
     */
    private $album;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
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
     * @param \GalleryBundle\Entity\Album\Album $album
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $content
     * @param string $title
     */
    public function __construct(Album $album, Language $language, $title)
    {
        $this->album = $album;
        $this->language = $language;
        $this->title = $title;
    }

    /**
     * @return \GalleryBundle\Entity\Album\Album
     */
    public function getAlbum()
    {
        return $this->album;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return \GalleryBundle\Entity\Album\Translation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}
