<?php

namespace PageBundle\Entity\Frame;

use CommonBundle\Entity\General\Language;
use PageBundle\Entity\Frame;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the translations of a frame item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame\Translation")
 * @ORM\Table(name="frames_translations")
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
     * @var Frame The frame of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Frame", inversedBy="translations")
     * @ORM\JoinColumn(name="frame", referencedColumnName="id", onDelete="CASCADE")
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @param Frame $frame
     * @param Language $language
     * @param string $description
     */
    public function __construct(Frame $frame, Language $language, string $description)
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
     * @return Frame
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
