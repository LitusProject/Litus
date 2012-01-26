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
	Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a group of users and is capable of determining which rights those users have.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Acl\Role")
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
     * @var \CommonBundle\Entity\Acl\Role $parents The parents of this role
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @JoinTable(
     *      name="acl.roles_inheritance",
     *      joinColumns={@JoinColumn(name="parent", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="child", referencedColumnName="name")}
     * )
     */
    private $parents;

    /**
     * @var \CommonBundle\Entity\Acl\Role $actions The actions that this role can execute
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Action")
     * @JoinTable(
     *      name="acl.roles_actions",
     *      joinColumns={@JoinColumn(name="role", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="action", referencedColumnName="id")}
     * )
     */
    private $actions;

    /**
     * @param string $name The name of the role
     * @param array $parents The role's parents
     */
    public function __construct($name, array $parents = array())
    {
        $this->name = $name;

        $this->parents = new ArrayCollection($parents);
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
     * @return array
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions->toArray();
    }

    /**
     * Add a new action to the allow list of this Role.
     *
     * @param \CommonBundle\Entity\Acl\Action $action
     * @return void
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
            $acl->getAcl()->isAllowed(
                $this->getName(), $resource, $action
            )
        ) {
            return true;
        }

        foreach ($this->getParents() as $parent) {
            if ($parent->isAllowed($resource, $action))
                return true;
        }

        return false;
    }
}