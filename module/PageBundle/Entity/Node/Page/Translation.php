<?php

namespace PageBundle\Entity\Node\Page;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use PageBundle\Entity\Node\Page;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Node\Page\Translation")
 * @ORM\Table(name="nodes_pages_translations")
 */
class Translation
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Page The page of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page", inversedBy="translations")
     * @ORM\JoinColumn(name="page", referencedColumnName="id")
     */
    private $page;

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
     * @param Page     $page
     * @param Language $language
     * @param string   $title
     * @param string   $content
     */
    public function __construct(Page $page, Language $language, $title, $content)
    {
        $this->page = $page;
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
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
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
