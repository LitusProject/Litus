<?php

namespace CommonBundle\Entity\User;

use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\Preference;

/**
 * This is the entity that maps a user to a preference. This will store information about whether a user has a
 * certain preference.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\PreferenceMapping")
 * @ORM\Table(name="users_preference_map")
 */
class PreferenceMapping
{
    /**
     * @var integer The ID of this PreferenceMapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person this PreferenceMapping belongs to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="preferences")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Preference The preference that this PreferenceMapping is about
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\Preference", inversedBy="preferenceMappings")
     * @ORM\JoinColumn(name="preference", referencedColumnName="id")
     */
    private $preference;

    /**
     * @var boolean The boolean that defines the user's preference
     *
     * @ORM\Column(type="boolean")
     */
    private $value;

    /**
     * @param Academic   $person
     * @param Preference $preference
     * @param boolean    $value
     */
    public function __construct(Academic $person, Preference $preference, bool $value = true)
    {
        $this->person = $person;
        $this->preference = $preference;
        $this->value = $value;

        $person->addPreferenceMapping($this);
        $preference->addPreferenceMapping($this);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Academic
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Preference
     */
    public function getPreference()
    {
        return $this->preference;
    }

    /**
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param boolean $value
     *
     * @return self
     */
    public function setValue(bool $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param array $preferences
     *
     * @return boolean
     */
    public function inPreferences($preferences)
    {
        foreach ($preferences as $preference) {
            if ($this->getPreference()->getName() == $preference->getName()) {
                return true;
            }
        }
        return false;
    }
}
