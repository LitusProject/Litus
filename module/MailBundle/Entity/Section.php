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
     * Creates a new newsletter section with the given name.
     *
     * @param string $name The name for this newsletter section.
     */
    public function __construct($name, $attribute)
    {
        $this->name = $name;
        $this->attribute = $attribute;
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }
}