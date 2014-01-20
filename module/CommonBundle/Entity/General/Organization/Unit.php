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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Organization;

use CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\General\Organization,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a unit of the organization.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Organization\Unit")
 * @ORM\Table(name="general.organizations_units")
 */
class Unit
{
    /**
     * @var integer The ID of this unit
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The unit's name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The unit's mail
     *
     * @ORM\Column(type="string")
     */
    private $mail;

    /**
     * @var \CommonBundle\Entity\General\Organization The unit's organization
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private $organization;

    /**
     * @var \CommonBundle\Entity\General\Organization\Unit The unit's parent
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization\Unit", cascade={"persist"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles associated with the unit
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="general.organizations_units_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="unit", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles associated with the coordinator of the unit
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="general.organizations_units_coordinator_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="unit", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $coordinatorRoles;

    /**
     * @var boolean Whether or not this unit is displayed on the site
     *
     * @ORM\Column(type="boolean")
     */
    private $displayed;

    /**
     * @var boolean Whether or not this unit is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param string $name The unit's name
     * @param string $mail The unit's mail
     * @param \CommonBundle\Entity\General\Organization $organization The unit's organization
     * @param array $roles The roles associated with the unit
     * @param array $coordinatorRoles The roles associated with the coordinator of the unit
     * @param boolean $displayed Whether or not this unit is displayed on the site
     * @param \CommonBundle\Entity\General\Organization\Unit $parent The unit's parent
     */
    public function __construct($name, $mail, Organization $organization, array $roles, array $coordinatorRoles, $displayed, Unit $parent = null)
    {
        $this->name = $name;
        $this->mail = $mail;
        $this->organization = $organization;
        $this->parent = $parent;
        $this->displayed = $displayed;
        $this->active = true;

        $this->roles = new ArrayCollection($roles);
        $this->coordinatorRoles = new ArrayCollection($coordinatorRoles);
    }

    /**
     * @return string
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
     * @param string $name
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param \CommonBundle\Entity\General\Organization $organization
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \CommonBundle\Entity\General\Organization\Unit $unit
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setParent(Unit $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @param boolean $mergeParentRoles
     * @return array
     */
    public function getRoles($mergeParentRoles = true)
    {
        return array_merge(
            $mergeParentRoles && null !== $this->getParent()
                ? $this->getParent()->getRoles()
                : array(),
            $this->roles->toArray()
        );
    }

    /**
     * @param array $roles
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setRoles(array $roles)
    {
        $this->roles = new ArrayCollection($roles);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\Acl\Role $role
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function removeRole(Role $role) {
        $this->roles->removeElement($role);
        return $this;
    }

    /**
     * @param boolean $mergeParentRoles
     * @return array
     */
    public function getCoordinatorRoles($mergeParentRoles = true)
    {
        return array_merge(
            $mergeParentRoles && null !== $this->getParent()
                ? $this->getParent()->getCoordinatorRoles()
                : array(),
            $this->coordinatorRoles->toArray()
        );
    }

    /**
     * @param array $coordinatorRoles
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setCoordinatorRoles(array $coordinatorRoles)
    {
        $this->coordinatorRoles = new ArrayCollection($coordinatorRoles);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\Acl\Role $coordinatorRole
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function removeCoordinatorRole(Role $coordinatorRole) {
        $this->coordinatorRoles->removeElement($coordinatorRole);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisplayed()
    {
        return $this->displayed;
    }

    /**
     * @param boolean $displayed
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setDisplayed($displayed)
    {
        $this->displayed = $displayed;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }
}
