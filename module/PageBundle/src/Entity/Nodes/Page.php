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
    Doctrine\Common\Collections\ArrayCollection,
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
     * @var \PageBundle\Entity\Nodes\Page The page's parent
     *
     * @ManyToOne(targetEntity="\PageBundle\Entity\Category")
     * @JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var \PageBundle\Entity\Nodes\Page The page's parent
     *
     * @ManyToOne(targetEntity="\PageBundle\Entity\Nodes\Page")
     * @JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var array The translations of this page
     *
    * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
    * @JoinTable(
    *      name="nodes.pages_roles_map",
    *      joinColumns={@JoinColumn(name="page", referencedColumnName="id")},
    *      inverseJoinColumns={@JoinColumn(name="role", referencedColumnName="name")}
    * )
     */
    private $editRoles;

    /**
     * @var string The name of this tanslation
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @var array The translations of this page
     *
     * @OneToMany(targetEntity="PageBundle\Entity\Nodes\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \PageBunlde\Entity\Node\Page $parent
     * @param array $editGroups
     * @param string $name
     */
    public function __construct(Person $person, $name, Category $category, array $editGroups, Page $parent = null)
    {
        parent::__construct($person);

        $this->startTime = new DateTime('now');

        $this->setParent($parent);
        $this->editGroups = new ArrayCollection($editGroups);

        $this->name = str_replace(' ', '-', strtolower($name));
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $endTime
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return \DateTime
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \PageBundle\Entity\Nodes\Page $category The page's category
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setParent(Page $parent)
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
     * @param array $editGroups
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setEditGroups(array $editGroups)
    {
        $this->editGroups = new ArrayCollection($editGroups);
        return $this;
    }

    /**
     * @return array
     */
    public function getEditGroups()
    {
        return $this->editGroups->toArray();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
