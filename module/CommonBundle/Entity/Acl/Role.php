<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Acl;

use CommonBundle\Component\Acl\Acl;
use CommonBundle\Entity\Acl\Action;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a group of users and is capable of determining which rights those users have.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Acl\Role")
 * @ORM\Table(name="acl.roles")
 */
class Role
{
    /**
     * @var string The name of the role
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var boolean Whether or not this is a system role
     *
     * @ORM\Column(type="boolean")
     */
    private $system;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The role's parents
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="acl.roles_inheritance_map",
     *      joinColumns={@ORM\JoinColumn(name="child", referencedColumnName="name")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent", referencedColumnName="name")}
     * )
     */
    private $parents;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The role's actions
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Action")
     * @ORM\JoinTable(
     *      name="acl.roles_actions_map",
     *      joinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="action", referencedColumnName="id")}
     * )
     */
    private $actions;

    /**
     * @param string  $name    The name of the role
     * @param boolean $system  Whether or not this is a system role
     * @param array   $parents The role's parents
     * @param array   $actions The role's actions
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
     * @param  array $parents
     * @return self
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
     * @param  array $actions
     * @return self
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
     * @param  Action $action The action the role should have access to
     * @return self
     */
    public function addAction(Action $action)
    {
        $this->actions->add($action);

        return $this;
    }

    /**
     * Remove the given action from the role.
     *
     * @param  Action $action The action the role should have access to
     * @return Role
     */
    public function removeAction(Action $action)
    {
        $this->actions->removeElement($action);

        return $this;
    }

    /**
     * Checks whether or not this role has sufficient permissions to access
     * the specified action.
     *
     * @param  Acl    $acl      The ACL instance
     * @param  string $resource The resource the action belongs to
     * @param  string $action   The action that should be verified
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
            if ($parent->isAllowed($acl, $resource, $action)) {
                return true;
            }
        }

        return false;
    }
}
