<?php

namespace Litus\Entity\Acl;

/**
 * Class that represents an action that can be executed on a certain {@see \Litus\Entity\Acl\Resource}.
 *
 * Example, DELETE a forum post, COOK a contact form
 *
 * @Entity(repositoryClass="Litus\Repository\Acl\Action")
 * @Table(
 *      name="acl.actions"),
 *      uniqueConstraints={@UniqueConstraint(name="action_unique", columns={"name", "resource"})}
 * )
 */
class Action
{
    /**
     * @var int The ID of this action
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string $name The name of the action
     *
     * @Column(type="string")
     */
    private $name;

	/**
     * @var \Litus\Entity\Acl\Resource The name of the resource
     *
     * @ManyToOne(targetEntity="Litus\Entity\Acl\Resource")
     * @JoinColumn(name="resource", referencedColumnName="name")
     */
    private $resource;

    /**
     * @param string $name The name of the action
     * @param \Litus\Entity\Acl\Resource $resource The resource to which the action belongs
     */
    public function __construct($name, Resource $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
    }

    /**
     * @return int
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
     * @return \Litus\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}