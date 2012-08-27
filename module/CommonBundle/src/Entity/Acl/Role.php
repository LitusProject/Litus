<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Acl;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Entity\Acl\Action,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a group of users and is capable of determining which rights those users have.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Acl\Role")
 * @Table(name="acl.roles")
 */
class Role
{
    /**
     * @var string The name of the role
     *
     * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * @var boolean Whether or not this is a system role
     *
     * @Column(type="boolean")
     */
    private $system;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The role's parents
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @JoinTable(
     *      name="acl.roles_inheritance_map",
     *      joinColumns={@JoinColumn(name="child", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="parent", referencedColumnName="name")}
     * )
     */
    private $parents;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The role's actions
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Action")
     * @JoinTable(
     *      name="acl.roles_actions_map",
     *      joinColumns={@JoinColumn(name="role", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="action", referencedColumnName="id")}
     * )
     */
    private $actions;

    /**
     * @param string $name The name of the role
     * @param boolean $system Whether or not this is a system role
     * @param array $parents The role's parents
     * @param array $actions The role's actions
     */
    public function __construct($name, $system = false, array $parents = array(), array $actions = array())
    {
        $this->name = $name;
        $this->system = $system;

        $this->parents = new ArrayCollection($parents);
        $this->actions = new ArrayCollection($actions);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param array $parents
     * @return \CommonBundle\Entity\Acl\Role
     */
    public function setParents(array $parents)
    {
        $this->parents = new ArrayCollection($parents);

        return $this;
    }

    /**
     * @return array
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }

    /**
     * @param array $actions
     * @return \CommonBundle\Entity\Acl\Role
     */
    public function setActions(array $actions)
    {
        $this->actions = new ArrayCollection($actions);

        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions->toArray();
    }

    /**
     * Allow this role access to the given action.
     *
     * @param \CommonBundle\Entity\Acl\Action $action The action the role should have access to
     * @return \CommonBundle\Entity\Acl\Role
     */
    public function allow(Action $action)
    {
        $this->actions->add($action);
    }

    /**
     * Checks whether or not this role has sufficient permissions to access
     * the specified action.
     *
     * @param \CommonBundle\Component\Acl\Acl $acl The ACL instance
     * @param string $resource The resource the action belongs to
     * @param string $action The action that should be verified
     * @return bool
     */
    public function isAllowed(Acl $acl, $resource, $action)
    {
        if (
            $acl->isAllowed(
                $this->getName(), $resource, $action
            )
        ) {
            return true;
        }

        foreach ($this->getParents() as $parent) {
            if ($parent->isAllowed($acl, $resource, $action))
                return true;
        }

        return false;
    }
}
