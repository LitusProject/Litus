<?php

namespace MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a newsletter section.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Section")
 * @ORM\Table(name="mail_sections")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *
 * })
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
     * Creates a new newsletter section with the given name.
     *
     * @param string $name The name for this newsletter section.
     * @param string $attribute The SendInBlue attribute that corresponds to this newsletter section
     * @param bool $defaultValue The default preference value of this newsletter section for each user
     */
    public function __construct($name, $attribute=null, $defaultValue)
    {
        $this->name = $name;
        if ($attribute == null) {
            $this->attribute = $name;
        }
        else {
            $this->attribute = $attribute;
        }
        $this->defaultValue = $defaultValue;
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


}