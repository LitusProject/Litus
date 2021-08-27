<?php

namespace PageBundle\Entity\Node;

use CommonBundle\Component\Util\Url;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;
use PageBundle\Entity\Category;
use PageBundle\Entity\Node\Page\Translation;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Node\Page")
 * @ORM\Table(name="nodes_pages")
 */
class Page extends \CommonBundle\Entity\Node
{
    /**
     * @var DateTime The time at which this version was created
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var DateTime The time at which this version was rendered obsolete
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var Category The page's category
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var ArrayCollection The roles that can edit this page
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="nodes_pages_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="page", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @var Page|null The page's parent
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
     * @var ArrayCollection The translations of this page
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Node\Page\Translation", mappedBy="page", cascade={"remove"})
     */
    private $translations;

    /**
     * @var integer|null The ordering number for the page in the category
     *
     * @ORM\Column(name="order_number", type="integer", nullable=true)
     */
    private $orderNumber;

    /**
     * @var Language|null The Language of the forced language (null if it's a normal page)
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="forced_language", referencedColumnName="id", nullable=true)
     */
    private $forcedLanguage;

    /**
     * @var boolean reflects if the page is active.
     *
     * @ORM\Column(name="active", type="boolean", options={"default" = true})
     */
    private $active;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);

        $this->startTime = new DateTime();

        $this->editRoles = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param DateTime $endTime
     * @return self
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param Category $category
     * @return self
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param array $editRoles
     * @return self
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
     * @param Page $parent
     * @return self
     */
    public function setParent(Page $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Page|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = Url::createSlug($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     * @return Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        $fallbackTranslation = null;

        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback) {
            return $fallbackTranslation;
        }

        return null;
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getTitle();
        }

        return '';
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getContent();
        }

        return '';
    }

    /**
     * Closes this version of the page.
     *
     * @return void
     */
    public function close()
    {
        if ($this->endTime === null) {
            $this->endTime = new DateTime();
        }
    }

    /**
     * Checks whether or not the given user can edit the page.
     *
     * @param Person|null $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if ($person === null) {
            return false;
        }

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return integer
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param $orderNumber
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        if ($orderNumber === null || gettype($orderNumber) !== 'int') {
            $this->orderNumber = null;
        } else {
            $this->orderNumber = $orderNumber;
        }
        return $this;
    }

    /**
     * @return Language|null
     */
    public function getForcedLanguage()
    {
        return $this->forcedLanguage;
    }

    /**
     * @param $forcedLanguage
     * @return self
     */
    public function setForcedLanguage($forcedLanguage)
    {
        // phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
        if ($forcedLanguage === null || get_class($forcedLanguage) !== Language::class) {
        // phpcs:enable
            $this->forcedLanguage = null;
        } else {
            $this->forcedLanguage = $forcedLanguage;
        }
        return $this;
    }

    /**
     * @param Language $lang
     * @return boolean
     */
    public function isLanguageAvailable(Language $lang)
    {
        return $this->getForcedLanguage() == null || $this->getForcedLanguage() === $lang;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return self
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
        return $this;
    }
}
