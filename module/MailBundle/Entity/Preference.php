<?php

namespace MailBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use CommonBundle\Entity\User\PreferenceMapping;

/**
 * This is the entity for a mailing preference.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Preference")
 * @ORM\Table(name="mail_preferences")
 */

class Preference
{
    /**
     * @var integer The unique identifier of this mailing preference
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this mailing preference that will be shown on the account page
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The description of this mailing preference that will be shown on the account page
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string The attribute name of this mailing preference in SendInBlue
     *
     * @ORM\Column(type="string")
     */
    private $attribute;

    /**
     * @var bool The default preference value of this mailing preference for each user
     *
     * @ORM\Column(name="default_value", type="boolean")
     */
    private $defaultValue;

    /**
     * @var bool If set, this preference is used by users to indicate if they want a certain subject to be present in their personalized newsletter or not
     *
     * @ORM\Column(name="is_newsletter", type="boolean")
     */
    private $isNewsletter;

    /**
     * @var ArrayCollection The preferenceMappings that refer to this preference
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\PreferenceMapping", mappedBy="preference", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $preferenceMappings;

    /**
     * Creates a new mailing preference.
     *
     * @param string $name The name of this mailing preference that will be shown on the account page
     * @param string $description The description of this mailing preference that will be shown on the account page
     * @param string $attribute The attribute name of this mailing preference in SendInBlue
     * @param bool $defaultValue The default preference value of this mailing preference for each user
     * @param bool $isNewsletter If set, this preference is used by users to indicate if they want a certain subject to be present in their personalized newsletter or not
     */
    public function __construct($name=null, $description=null, $attribute=null, $defaultValue=false, $isNewsletter=false) {
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
    public function getDescription() {
        return $this->description;
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
    public function getPreferenceMappings()
    {
        return $this->preferenceMappings;
    }

    /**
     * @return bool
     */
    public function isNewsletter()
    {
        return $this->isNewsletter;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name)
    {
        if ($this->isNewsletter) {
            $this->name = "newsletter_" . $name;
        }
        else {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description) {
        $this->description = $description;

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
     * @param bool $isNewsletter
     *
     * @return $this
     */
    public function setIsNewsletter(bool $isNewsletter)
    {
        $this->isNewsletter = $isNewsletter;

        // when newsletter is set, name should always start with "newsletter_"
        if ( $isNewsletter && !(strncmp($this->name, "newsletter_", 11) === 0)) {
            $this->name = "newsletter_" . $this->name;
        }
        if ( !$isNewsletter && strncmp($this->name, "newsletter_", 11) === 0) {
            $this->name = substr($this->name, 11);
        }

        return $this;
    }

    /**
     * @param  PreferenceMapping $preferenceMapping
     * @return self
     */
    public function addPreferenceMapping(PreferenceMapping $preferenceMapping)
    {
        $this->preferenceMappings->add($preferenceMapping);

        return $this;
    }

    /**
     * @param  PreferenceMapping $preferenceMapping
     * @return self
     */
    public function removePreferenceMapping(PreferenceMapping $preferenceMapping)
    {
        $this->preferenceMappings->removeElement($preferenceMapping);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAllPreferenceMappings()
    {
        $this->preferenceMappings = new ArrayCollection();

        return $this;
    }

    /**
     * @return bool
     */
    public function inPreferencesMappings($preferenceMappingsToCheck) {
        foreach ($preferenceMappingsToCheck as $preferenceMapping) {
            if ($this->name == $preferenceMapping->getPreference()->getName()) {
                return true;
            }
        }
        return false;
    }

}