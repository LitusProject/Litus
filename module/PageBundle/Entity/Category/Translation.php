<?php

namespace PageBundle\Entity\Category;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use PageBundle\Entity\Category;

/**
 * This entity represents a translation of a category.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Category\Translation")
 * @ORM\Table(name="nodes_pages_categories_translations")
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
     * @var Category The category of this translation
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Category", inversedBy="translations")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

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
     * @param Category $category
     * @param Language $language
     * @param string   $name
     */
    public function __construct(Category $category, Language $language, $name)
    {
        $this->category = $category;
        $this->language = $language;
        $this->name = $name;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
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
}
