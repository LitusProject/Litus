<?php

namespace Litus\Entities\Acl;

/**
 * Class that represents an Action that can be executed on a certain {@see \Litus\Entities\Acl\Resource}.
 *
 * Example, DELETE a forum post, COOK a contact form
 *
 * @Entity(repositoryClass="Litus\Repositories\Acl\Action")
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
     * @var \Litus\Entities\Acl\Resource The name of the resource
     *
     * @ManyToOne(targetEntity="Litus\Entities\Acl\Resource", cascade={"ALL"}, fetch="LAZY")
     * @JoinColumn(name="name", referencedColumnName="name")
     */
    private $resource;

    /**
     * @var string $name The name of the action
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @param string $name The name of the action
     * @param \Litus\Entities\Acl\Resource $resource The resource to which the action belongs
     */
    public function __construct($name, Resource $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
    }

    /**
     * @return \Litus\Entities\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
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
}