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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Node\FAQ;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Node\FAQ\Translation")
 * @ORM\Table(name="nodes_faq_translation")
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
     * @var FAQ The faq of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Node\FAQ\FAQ", inversedBy="translations")
     * @ORM\JoinColumn(name="faq", referencedColumnName="id")
     */
    private $faq;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The title of this translation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The content of this translation
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param FAQ      $faq
     * @param Language $language
     * @param string   $title
     * @param string   $content
     */
    public function __construct(FAQ $faq, Language $language, $title, $content)
    {
        $this->faq = $faq;
        $this->language = $language;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return FAQ
     */
    public function getFAQ()
    {
        return $this->faq;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
