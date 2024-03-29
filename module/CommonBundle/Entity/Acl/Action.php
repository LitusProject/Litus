<?php

namespace CommonBundle\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that represents an action that can be executed on a certain resource.
 *
 * Examples:
 * DELETE a forum post, COOK a contact form, ...
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Acl\Action")
 * @ORM\Table(
 *      name="acl_actions"),
 *      uniqueConstraints={@ORM\UniqueConstraint(name="acl_actions_name_resource", columns={"name", "resource"})}
 * )
 */
class Action
{
    /**
     * @var integer The ID of this action
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string $name The name of the action
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \CommonBundle\Entity\Acl\Resource The name of the resource
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Acl\Resource")
     * @ORM\JoinColumn(name="resource", referencedColumnName="name")
     */
    private $resource;

    /**
     * @param string                            $name     The name of the action
     * @param \CommonBundle\Entity\Acl\Resource $resource The resource to which the action belongs
     */
    public function __construct($name, Resource $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
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
     * @return \CommonBundle\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
