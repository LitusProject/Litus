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

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    PageBundle\Entity\Category;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Nodes\Page")
 * @ORM\Table(name="nodes.pages")
 */
class Page extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var \Datetime The time at which this version was created
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \Datetime The time at which this version was rendered obsolete
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var \PageBundle\Entity\Nodes\Page The page's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles that can edit this page
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="nodes.pages_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="page", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @var \PageBundle\Entity\Nodes\Page The page's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Nodes\Page")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var string The name of this page
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this page
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Nodes\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $name
     * @param array $editRoles
     * @param string $name
     */
    public function __construct(Person $person, $name, Category $category, array $editRoles)
    {
        parent::__construct($person);

        $this->startTime = new DateTime();

        $this->category = $category;
        $this->name = Url::createSlug($name);

        $this->editRoles = new ArrayCollection($editRoles);
        $this->translations = new ArrayCollection();
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
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \PageBundle\Entity\Category $category
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setCategory($category)
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
     * @param array $editRoles
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function setEditRoles(array $editRoles)
    {
        $this->editRoles = new ArrayCollection($editRoles);
        return $this;
    }

    /**
     * @return array
     */
    public function getEditRoles()
    {
        return $this->editRoles->toArray();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \PageBundle\Entity\Nodes\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback)
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getTitle();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getContent();

        return '';
    }

    /**
     * Closes this version of the page.
     *
     * @return void
     */
    public function close()
    {
        if (null === $this->endTime)
            $this->endTime = new DateTime();
    }

    /**
     * Checks whether or not the given user can edit the page.
     *
     * @param \CommonBundle\Entity\Users\Person $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person)
    {
        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role))
                return true;
        }

        return false;
    }
}
