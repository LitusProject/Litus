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
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $category
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);
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