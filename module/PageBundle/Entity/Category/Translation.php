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

namespace PageBundle\Entity\Category;

use CommonBundle\Entity\General\Language,
    PageBundle\Entity\Category,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a translation of a category.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Category\Translation")
 * @ORM\Table(name="nodes.pages_categories_translations")
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
