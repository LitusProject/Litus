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

namespace PageBundle\Entity\Link;

use CommonBundle\Entity\General\Language,
    PageBundle\Entity\Link,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a translation of a link.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Links\Translation")
 * @ORM\Table(name="nodes.pages_links_translations")
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
     * @var \PageBundle\Entity\Link The link of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Link", inversedBy="translations")
     * @ORM\JoinColumn(name="link", referencedColumnName="id")
     */
    private $link;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this translation
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
     * @param \PageBundle\Entity\Link $link
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $name
     * @param string $url
     */
    public function __construct(Link $link, Language $language, $name, $url)
    {
        $this->link = $link;
        $this->language = $language;
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return \PageBundle\Entity\Link
     */
    public function getLink()
    {
        return $this->link;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \PageBundle\Entity\Link\Translation
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
     * @param string $url
     *
     * @return \PageBundle\Entity\Link\Translation
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
