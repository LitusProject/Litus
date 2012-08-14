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

namespace PageBundle\Entity\Categories;

use CommonBundle\Entity\General\Language;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="PageBundle\Repository\Categories\Translation")
 * @Table(name="nodes.pages_categories_translations")
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
     * @var \PageBundle\Entity\Category The category of this translation
     *
     * @ManyToOne(targetEntity="PageBundle\Entity\Category", inversedBy="translations")
     * @JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

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
     * @Column(type="string")
     */
    private $name;

    /**
     * @param \PageBundle\Entity\Category $category
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $name
     */
    public function __construct(Category $category, Language $language, $name)
    {
        $this->category = $category;
        $this->language = $language;
        $this->name = $name;
    }

    /**
     * @return \PageBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
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
     * @return \PageBundle\Entity\Categories\Translation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
