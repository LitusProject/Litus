<?php

namespace CommonBundle\Entity\General\Node\FAQ;

use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\General\Node\FAQ\Translation;
use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;

/**
 * This class represents a Frequently Asked Question that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Node\FAQ\FAQ")
 * @ORM\Table(name="nodes_faq")
 */
class FAQ extends \CommonBundle\Entity\Node
{
    /**
     * @var string The name for this FAQ
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection The roles that can edit this FAQ
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="faq_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="faq", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @var ArrayCollection The translations of this faq
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\General\Node\FAQ\Translation", mappedBy="faq", cascade={"remove"})
     */
    private $translations;

    /**
     * @var integer|null The ordering number for the faq on the page
     *
     * @ORM\Column(name="order_number", type="integer", nullable=true)
     */
    private $orderNumber;

    /**
     * @var Language|null The Language of the forced language (null if it's a normal faq)
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="forced_language", referencedColumnName="id", nullable=true)
     */
    private $forcedLanguage;

    /**
     * @var boolean reflects if the faq is active.
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);

        $this->active = true;
        $this->editRoles = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Checks whether the given user can edit the page.
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
        if ($forcedLanguage === null || $forcedLanguage::class !== Language::class) {
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
