<?php
 
namespace GalleryBundle\Entity\Album;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * This entity stores the album item.
 *
 * @Entity(repositoryClass="GalleryBundle\Repository\Album\Album")
 * @Table(name="gallery.album")
 */
class Album
{
    /**
     * @var int The ID of this album
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \DateTime The time of creation of this album
     *
     * @Column(name="create_time", type="datetime")
     */
    private $createTime;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person who created this album
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="create_person", referencedColumnName="id")
     */
    private $createPerson;
    
    /**
     * @var \DateTime The date the photo's of this album were created
     *
     * @Column(name="date_activity", type="datetime")
     */
    private $dateActivity;
    
    /**
     * @var array The translations of this album
     *
     * @OneToMany(targetEntity="GalleryBundle\Entity\Album\Translation", mappedBy="album", cascade={"remove"})
     */
    private $translations;
    
    /**
     * @var array The photos of this album
     *
     * @OneToMany(targetEntity="GalleryBundle\Entity\Album\Photo", mappedBy="album", cascade={"remove"})
     * @OrderBy({"id": "ASC"})
     */
    private $photos;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \DateTime $date
     */
    public function __construct(Person $person, DateTime $date)
    {
        $this->createTime = new DateTime();
        $this->createPerson = $person;
        $this->dateActivity = $date;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreatePerson()
    {
        return $this->createPerson;
    }
    
    /**
     * @param \DateTime $date
     *
     * @return \GalleryBundle\Entity\Album\Album
     */
    public function setDate(DateTime $date)
    {
        $this->dateActivity = $date;
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->dateActivity;
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \GalleryBundle\Entity\Album\Translation
     */
    public function getTranslation(Language $language)
    {
        foreach($this->translations as $translation) {
            if ($translation->getLanguage() == $language)
                return $translation;
        }
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getTitle(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getTitle();
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getName(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getName();
    }
    
    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }
    
    /**
     * @return \GalleryBundle\Entity\Album\Photo
     */
    public function getPhoto()
    {
        do {
            $num = rand(0, sizeof($this->photos) - 1);
        } while($this->photos[$num]->isCensored());
        return $this->photos[$num];
    }
}