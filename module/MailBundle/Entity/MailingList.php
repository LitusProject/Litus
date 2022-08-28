<?php

namespace MailBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList")
 * @ORM\Table(name="mail_lists")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "named"="MailBundle\Entity\MailingList\Named"
 * })
 */
abstract class MailingList
{
    /**
     * @var integer The list's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var ArrayCollection The entries of this list
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\MailingList\Entry", mappedBy="list", cascade={"remove"})
     */
    private $entries;

    /**
     * @var ArrayCollection The admins of this list
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\MailingList\AdminMap", mappedBy="list", cascade={"remove"})
     */
    private $admins;

    /**
     * @var ArrayCollection The admins of this list
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\MailingList\AdminRoleMap", mappedBy="list", cascade={"remove"})
     */
    private $adminRoles;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->admins = new ArrayCollection();
        $this->adminRoles = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return ArrayCollection
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * @return ArrayCollection
     */
    public function getAdminRoles()
    {
        return $this->adminRoles;
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * This method checks whether the list can be edited by the given academic.
     *
     * @param  Academic $academic  The academic that should be checked
     * @param  boolean  $editAdmin Whether or not to check for permission to edit the admins of the list
     * @return boolean
     */
    public function canBeEditedBy(Academic $academic, $editAdmin = false)
    {
        foreach ($academic->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                return true;
            }

            foreach ($this->adminRoles as $adminRole) {
                if ($adminRole->getRole() == $role) {
                    return !($editAdmin && !$adminRole->canEditAdmin());
                }
            }
        }

        foreach ($this->admins as $admin) {
            if ($admin->getAcademic() == $academic) {
                return !($editAdmin && !$admin->canEditAdmin());
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->getName() . '@vtk.be';
    }
}
