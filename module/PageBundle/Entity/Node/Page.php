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

namespace PageBundle\Entity\Node;

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    PageBundle\Entity\Category,
    Locale;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Node\Page")
 * @ORM\Table(name="nodes.pages")
 */
class Page extends \CommonBundle\Entity\Node
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
     * @var \PageBundle\Entity\Node\Page The page's category
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
     * @var \PageBundle\Entity\Node\Page The page's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
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
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Node\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param string                           $name
     * @param array                            $editRoles
     * @param string                           $name
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
     * @param  \DateTime                    $endTime
     * @return \PageBundle\Entity\Node\Page
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
     * @param  \PageBundle\Entity\Category  $category
     * @return \PageBundle\Entity\Node\Page
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
     * @param  array                        $editRoles
     * @return \PageBundle\Entity\Node\Page
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
     * @return \PageBundle\Entity\Node\Page
     */
    public function setParent(Page $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \PageBundle\Entity\Node\Page
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
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return \PageBundle\Entity\Node\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        $fallbackTranslation = null;

        foreach ($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback)
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
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
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
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
     * @param  \CommonBundle\Entity\User\Person $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if (null === $person)
            return false;

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor')
                return true;
        }

        return false;
    }
}
