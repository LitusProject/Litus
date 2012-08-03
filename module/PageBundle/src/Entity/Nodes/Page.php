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
    CommonBundle\Entity\Users\Person,
    DateTime,
    PageBundle\Entity\Category,
    PageBundle\Entity\Nodes\Page;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="PageBundle\Repository\Nodes\Page")
 * @Table(name="nodes.pages")
 */
class Page extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var \Datetime The time at which this version was created
     *
     * @Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \Datetime The time at which this version was rendered obsolete
     *
     * @Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;
    
    /**
     * @var \PageBundle\Entity\Category The page's category
     *
     * @ManyToOne(targetEntity="PageBundle\Entity\Category")
     */
    private $category;
    
    /**
     * @var \PageBundle\Entity\Category The page's parent
     *
     * @ManyToOne(targetEntity="PageBundle\Entity\Nodes\Page")
     */
    private $parent;
    
    /**
     * @var array The translations of this page
     *
     * @OneToMany(targetEntity="PageBundle\Entity\Nodes\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \PageBundle\Entity\Category $category The page's category
     * 
     */
    public function __construct(Person $person, Category $category, Page $parent)
    {
        parent::__construct($person);
        
        $this->startTime = new DateTime('now');
        
        $this->setCategory($category);
        $this->setParent($parent);
    }
    
    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    /**
     * @param \PageBundle\Entity\Category $category The page's category
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        
        return $this;
    }
    
    /**
     * @return \PageBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param \PageBundle\Entity\Nodes\Page $category The page's category
     */
    public function setParent(Page $category)
    {
        $this->parent = $parent;
        
        return $this;
    }
    
    /**
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * @param \CommonBundle\Entity\General\Language $language
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
     * @return string
     */
    public function getContent(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getContent();
    }
    
    /**
     * Closes this version of the page.
     *
     * @return void
     */
    public function close()
    {
        if (null === $this->endTime)
            $this->endTime = new DateTime('now');
    }
}
