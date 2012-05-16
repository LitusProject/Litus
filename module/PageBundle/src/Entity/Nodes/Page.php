<?php
 
namespace PageBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="PageBundle\Repository\Nodes\Page")
 * @Table(name="nodes.page")
 */
class Page extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var array The translations of this page
     *
     * @OneToMany(targetEntity="PageBundle\Entity\Nodes\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \PageBundle\Entity\Nodes\Translation
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
}