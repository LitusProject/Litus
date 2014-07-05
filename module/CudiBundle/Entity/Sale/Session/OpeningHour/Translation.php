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

namespace CudiBundle\Entity\Sale\Session\OpeningHour;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\OpeningHour\Translation")
 * @ORM\Table(name="cudi.sales_session_openinghours_translations")
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
     * @var OpeningHour The opening hour of this translation
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour", inversedBy="translations")
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
     * @return int
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
