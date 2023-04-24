<?php

namespace MailBundle\Entity\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\Section;
use Doctrine\ORM\PersistentCollection;

/**
 * This is the entity for a newsletter section.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Section\Group")
 * @ORM\Table(name="mail_sections_groups")
 */

class Group
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
     * @var string The name of this group
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection The children sections of this group
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\Section", mappedBy="section_group", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $children;

    /**
     * Creates a new section group with the given name.
     *
     * @param string $name The name of this group that will also be shown
     */
    public function __construct($name = "")
    {
        $this->name = $name;
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param Section $child
     * @return $this
     */
    public function addChild(Section $child)
    {
        $this->children->add($child);

        return $this;
    }

    /**
     * @param Section $child
     * @return $this
     */
    public function removeChild(Section $child)
    {
        $this->children->removeElement($child);

        return $this;
    }
}