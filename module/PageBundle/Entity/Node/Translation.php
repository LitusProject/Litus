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

namespace PageBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Nodes\Translation")
 * @ORM\Table(name="nodes.pages_translations")
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
     * @var \PageBundle\Entity\Node\Page The page of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page", inversedBy="translations")
     * @ORM\JoinColumn(name="page", referencedColumnName="id")
     */
    private $page;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this translation
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
     * @param \PageBundle\Entity\Node\Page $page
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $title
     * @param string $content
     */
    public function __construct(Page $page, Language $language, $title, $content)
    {
        $this->page = $page;
        $this->language = $language;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return \PageBundle\Entity\Node\Page
     */
    public function getPage()
    {
        return $this->page;
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
     * @return \PageBundle\Entity\Node\Translation
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
     * @param string $content
     *
     * @return \PageBundle\Entity\Node\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
