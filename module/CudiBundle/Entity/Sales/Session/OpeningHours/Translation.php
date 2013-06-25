<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sales\Session\OpeningHours;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Session\OpeningHours\Translation")
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
     * @var \CudiBundle\Repository\Sales\Session\OpeningHours\OpeningHour The opening hour of this translation
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour", inversedBy="translations")
     * @ORM\JoinColumn(name="opening_hour", referencedColumnName="id")
     */
    private $openingHour;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this translation
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
     * @param \CudiBundle\Repository\Sales\Session\OpeningHours\OpeningHour $openingHour
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $comment
     */
    public function __construct(OpeningHour $openingHour, Language $language, $comment)
    {
        $this->openingHour = $openingHour;
        $this->language = $language;
        $this->comment = $comment;
    }

    /**
     * @return \CudiBundle\Repository\Sales\Session\OpeningHours\OpeningHour
     */
    public function getOpeningHour()
    {
        return $this->openingHour;
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
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return \CudiBundle\Repository\Sales\Session\OpeningHours\Translation
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}
