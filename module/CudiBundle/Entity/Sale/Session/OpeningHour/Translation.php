<?php

namespace CudiBundle\Entity\Sale\Session\OpeningHour;

use CommonBundle\Entity\General\Language;
use CudiBundle\Entity\Sale\Session\OpeningHour;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\OpeningHour\Translation")
 * @ORM\Table(name="cudi_sale_sessions_opening_hours_translations")
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
     * @var OpeningHour The opening hour of this translation
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session\OpeningHour", inversedBy="translations")
     * @ORM\JoinColumn(name="opening_hour", referencedColumnName="id")
     */
    private $openingHour;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The comment of this translation
     *
     * @ORM\Column(type="string")
     */
    private $comment;

    /**
     * @param OpeningHour $openingHour
     * @param Language    $language
     * @param string      $comment
     */
    public function __construct(OpeningHour $openingHour, Language $language, $comment)
    {
        $this->openingHour = $openingHour;
        $this->language = $language;
        $this->comment = $comment;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return OpeningHour
     */
    public function getOpeningHour()
    {
        return $this->openingHour;
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
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
