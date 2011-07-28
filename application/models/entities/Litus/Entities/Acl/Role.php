<?php

namespace Litus\Entities\Acl;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a group of users and is capable of determining which rights those users have.
 *
 * @Entity(repositoryClass="Litus\Repositories\Acl\Role")
 * @Table(
 *      name="acl.roles",
 *      uniqueConstraints={@UniqueConstraint(name="role_unique_name", columns={"name"})}
 * )
 */
class Role
{
    /**
     * @var string $name The name of the Role
     *
     * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * The parents of this role
     *
     * @var Litus\Entities\Acl\Role $parents
     *
     * @ManyToMany(targetEntity="Litus\Entities\Acl\Role", cascade={"ALL"}, fetch="LAZY")
     * @JoinTable(
     *      name="acl.roles_inheritance",
     *      joinColumns={@JoinColumn(name="parent", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="child", referencedColumnName="name")}
     * )
     */
    private $parents;

    /**
     * @var \Litus\Entities\Acl\Role $actions The actions that this role can execute
     *
     * @ManyToMany(targetEntity="Litus\Entities\Acl\Action", cascade={"ALL"}, fetch="LAZY")
     * @JoinTable(
     *      name="acl.roles_actions",
     *      joinColumns={@JoinColumn(name="role", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="action", referencedColumnName="id")}
     * )
     */
    private $actions;

    /**
     * @param string $name The name of the role
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->parents = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add a new action to the allow list of this Role
     *
     * @param \Litus\Entities\Acl\Action $action
     * @return void
     */
    public function allow(Action $action)
    {
        $this->getActions()->add($action);
    }
}