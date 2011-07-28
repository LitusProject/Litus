<?php

namespace Litus\Entities\Acl;

/**
 * Class that represents a resource that can get accessed and/or manipulated, for example, a forum post, or a contact
 * form.
 *
 * @Entity(repositoryClass="Litus\Repositories\Acl\Resource")
 * @Table(
 *      name="acl.resources",
 *      uniqueConstraints={@UniqueConstraint(name="resource_unique_name", columns={"name"})}
 * )
 */
class Resource
{
    /**
     * @var string $name The name of this resource
     *
     * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * @var \Litus\Entities\Acl\Resource The parent of this resource
     *
     * @OneToOne(targetEntity="Litus\Entities\Acl\Resource", cascade={"ALL"}, fetch="LAZY")
     * @JoinColumn(name="parent", referencedColumnName="name")
     */
    private $parent;

    /**
     * @param string $name The name of the resource
     * @param \Litus\Entities\Acl\Resource $parent The parent of the resource, or null if there is no parent
     */
    public function __construct($name, Resource $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Litus\Entities\Acl\Resource
     */
    public function getParent()
    {
        return $this->parent;
    }
}
