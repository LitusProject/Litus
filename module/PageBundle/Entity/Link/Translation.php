<?php

namespace PageBundle\Entity\Link;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use PageBundle\Entity\Link;

/**
 * This entity represents a translation of a link.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Link\Translation")
 * @ORM\Table(name="nodes_pages_links_translations")
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
     * @var Link The link of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Link", inversedBy="translations")
     * @ORM\JoinColumn(name="link", referencedColumnName="id")
     */
    private $link;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The content of this translation
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The URL this link redirects to
     *
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @param Link     $link
     * @param Language $language
     * @param string   $name
     * @param string   $url
     */
    public function __construct(Link $link, Language $language, $name, $url)
    {
        $this->link = $link;
        $this->language = $language;
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Link
     */
    public function getLink()
    {
        return $this->link;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
