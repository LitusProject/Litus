<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="PageBundle\Repository\Nodes\Translation")
 * @Table(name="nodes.pages_translations")
 */
class Translation
{
    /**
     * @var int The ID of this tanslation
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \PageBundle\Entity\Nodes\Page The page of this translation
     *
     * @ManyToOne(targetEntity="PageBundle\Entity\Nodes\Page", inversedBy="translations")
     * @JoinColumn(name="page", referencedColumnName="id")
     */
    private $page;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The content of this tanslation
     *
     * @Column(type="text")
     */
    private $content;

    /**
     * @var string The title of this tanslation
     *
     * @Column(type="string")
     */
    private $title;

    /**
     * @var string The name of this tanslation
     *
     * @Column(type="string", unique=true)
     */
    private $name;

    /**
     * @param \PageBundle\Entity\Nodes\Page $page
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $content
     * @param string $title
     */
    public function __construct(Page $page, Language $language, $content, $title)
    {
        $this->page = $page;
        $this->language = $language;
        $this->content = $content;
        $this->title = $title;
        $this->_setName($title);
    }

    /**
     * @return \PageBundle\Entity\Nodes\Page
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
     * @param \PageBundle\Entity\Nodes\Page
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->_setName($title);
        return $this;
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
     * @param \PageBundle\Entity\Nodes\Page
     */
    private function _setName($name)
    {
        $this->name = str_replace(' ', '_', strtolower($name));
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
     * @param \PageBundle\Entity\Nodes\Page
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
