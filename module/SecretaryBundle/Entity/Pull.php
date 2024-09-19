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
     * @ORM\Column(name="study_nl", type="string")
     */
    private $study_nl;

    /**
     * @var string The study to which the pull belongs
     *
     * @ORM\Column(name="study_en", type="string")
     */
    private $study_en;

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

    /**
     * @var integer The amount of pulls of this study that are ordered
     *
     * @ORM\Column(name="ordered", type="integer", nullable=true)
     */
    private $ordered;

    /**
     * @var integer The amount that is still available
     *
     * @ORM\Column(name="amount_available", type="integer", nullable=true)
     */
    private $amount_available;

    public function __construct()
    {
        $this->ordered = 0;
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
    public function getStudyNl()
    {
        return $this->study_nl;
    }

    /**
     * @return string
     */
    public function getStudyEn()
    {
        return $this->study_en;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * @return integer
     */
    public function getOrdered()
    {
        return $this->ordered;
    }

    /**
     * @return integer
     */
    public function getAmountAvailable()
    {
        return $this->amount_available;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param string $study
     * @return self
     */
    public function setStudyNl($study)
    {
        $this->study_nl = $study;

        return $this;
    }

    /**
     * @param string $study
     * @return self
     */
    public function setStudyEn($study)
    {
        $this->study_en = $study;

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

    /**
     * @param integer $amount
     * @return self
     */
    public function setAmountAvailable($amount)
    {
        $this->amount_available = $amount;

        return $this;
    }

    /**
     * @return integer
     */
    public function addOrdered()
    {
        $this->ordered += 1;
        $this->amount_available -= 1;

        if ($this->amount_available == 0) {
            $this->available = false;
        }

        return $this->ordered;
    }
}
