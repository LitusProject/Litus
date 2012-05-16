<?php
 
namespace NewsBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="NewsBundle\Repository\Nodes\News")
 * @Table(name="nodes.news")
 */
class News extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var array The translations of this news
     *
     * @OneToMany(targetEntity="NewsBundle\Entity\Nodes\Translation", mappedBy="news", cascade={"remove"})
     */
    private $translations;
    
    /**
     * @var string The category of this news
     *
     * @Column(type="string")
     */
    private $category;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $category
     */
    public function __construct(Person $person, $category)
    {
        parent::__construct($person);
        $this->category = $category;
    }
    
    /** 
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param string $category
     *
     * @return \NewsBundle\Entity\Nodes\Translation
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \NewsBundle\Entity\Nodes\Translation
     */
    public function getTranslation(Language $language)
    {
        foreach($this->translations as $translation) {
            if ($translation->getLanguage() == $language)
                return $translation;
        }
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getTitle(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getTitle();
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getName(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getName();
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getContent(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getContent();
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getSummary(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getSummary();
    }
}