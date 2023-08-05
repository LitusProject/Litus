<?php

namespace MailBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CommonBundle\Entity\User\Preference;
use Doctrine\ORM\PersistentCollection;

/**
 * This is the entity for a newsletter section.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Section")
 * @ORM\Table(name="mail_sections")
 */

class Section
{
    /**
     * @var integer The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this newsletter section
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The attribute name of this newsletter section in SendInBlue
     *
     * @ORM\Column(type="string")
     */
    private $attribute;

    /**
     * @var bool The default preference value of this newsletter section for each user
     *
     * @ORM\Column(name="default_value", type="boolean")
     */
    private $defaultValue;

    /**
     * @var ArrayCollection The preferences that refer to this section
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Preference", mappedBy="section", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $preferences;

    /**
     * Creates a new newsletter section with the given name.
     *
     * @param string $name The name for this newsletter section.
     * @param string $attribute The SendInBlue attribute that corresponds to this newsletter section
     * @param bool $defaultValue The default preference value of this newsletter section for each user
     */
    public function __construct($name=null, $attribute=null, $defaultValue=false) {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return bool
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return ArrayCollection
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return self
     */
    public function setAttribute(string $attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param bool $defaultValue
     *
     * @return self
     */
    public function setDefaultValue(bool $defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param  Preference $preference
     * @return self
     */
    public function addPreference(Preference $preference)
    {
        $this->preferences->add($preference);

        return $this;
    }

    /**
     * @param  Preference $preference
     * @return self
     */
    public function removePreference(Preference $preference)
    {
        $this->preferences->removeElement($preference);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAllPreferences()
    {
        $this->preferences = new ArrayCollection();

        return $this;
    }

    /**
     * @return bool
     */
    public function inPreferences($preferencesToCheck) {
        foreach ($preferencesToCheck as $preference) {
            if ($this->name == $preference->getSection()->getName()) {
                return true;
            }
        }
        return false;
    }

}