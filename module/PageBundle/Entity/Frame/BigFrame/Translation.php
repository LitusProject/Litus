<?php

namespace PageBundle\Entity\Frame\BigFrame;

use CommonBundle\Entity\General\Language;
use PageBundle\Entity\Frame\BigFrame;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the translations of a big frame item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame\BigFrame\Translation")
 * @ORM\Table(name="frames_big_translations")
 */
class Translation
{
    /**
     * @var integer The ID of this translation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var BigFrame The frame of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Frame\BigFrame", inversedBy="translations")
     * @ORM\JoinColumn(name="frame", referencedColumnName="id")
     */
    private $frame;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The description of this translation
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @param BigFrame $frame
     * @param Language $language
     * @param string $description
     */
    public function __construct(BigFrame $frame, Language $language, string $description)
    {
        $this->frame = $frame;
        $this->language = $language;
        $this->description = $description;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BigFrame
     */
    public function getFrame()
    {
        return $this->frame;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }
}
