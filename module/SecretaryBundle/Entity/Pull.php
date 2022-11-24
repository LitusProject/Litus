<?php

namespace SecretaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the different pull options
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Pull")
 * @ORM\Table(name="secretary_pull")
 */
class Pull
{
    /**
     * @var integer The ID of the pull
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The study to which the pull belongs
     *
     * @ORM\Column(name="study", type="string")
     */
    private $study;

    /**
     * @var string The photopath of the pull
     *
     * @ORM\Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var boolean Whether or not the pull is available right now.
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStudy()
    {
        return $this->study;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * @return boolean
     */
    public function available()
    {
        return $this->available;
    }

    /**
     * @param string $study
     * @return self
     */
    public function setStudy($study)
    {
        $this->study = $study;

        return $this;
    }

    /**
     * @param string $photoPath
     * @return self
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * @param boolean $available
     * @return self
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }
}