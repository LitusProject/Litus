<?php

namespace CommonBundle\Entity\User;

use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\Section;

/**
 * This is the entity for a newsletter preference of a user.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Preference")
 * @ORM\Table(name="users_preferences")
 */
class Preference
{
    /**
     * @var integer The ID of this university status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person this newsletter preference belongs to
     *
     * @ORM\ManyToOne(
     *      targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="preferences"
     * )
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Section The section that this preference is about
     *
     * @ORM\Column(type="string")
     */
    private $section;

    /**
     * @var bool The boolean that defines the preference
     *
     * @ORM\Column(type="boolean")
     */
    private $value;

    /**
     * @param Academic $person
     * @param Section $section
     * @param bool $value
     */
    public function __construct(Academic $person, Section $section, bool $value = true)
    {
        $this->person = $person;
        $this->section = $section;
        $this->value = $value;
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
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool $value
     *
     * @return self
     */
    public function setValue(bool $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param array $sections
     *
     * @return bool
     */
    public function inSections(array $sections) {
        foreach ($sections as $section) {
            if ($this->getSection()->getName() == $section->getName()) {
                return true;
            }
        }
        return false;
    }

}