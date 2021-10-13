<?php

namespace CommonBundle\Entity\General;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents an organization entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Organization")
 * @ORM\Table(name="general_organizations")
 */
class Organization
{
    /**
     * @var integer The ID of the organization
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the organization
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
}
