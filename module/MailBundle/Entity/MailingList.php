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

namespace MailBundle\Entity;

use CommonBundle\Entity\User\Person\Academic,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList")
 * @ORM\Table(name="mail.lists")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "named"="MailBundle\Entity\MailingList\Named"
 * })
 */
abstract class MailingList
{
    /**
     * @var int The list's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var array The entries of this list
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\MailingList\Entry", mappedBy="list", cascade={"remove"})
     */
    private $entries;

    /**
     * @var array The admins of this list
     *
     * @ORM\OneToMany(targetEntity="MailBundle\Entity\MailingList\AdminMap", mappedBy="list", cascade={"remove"})
     */
    private $admins;

    /**
     * @var array The admins of this list
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * This method checks whether the list can be edited by the given academic.
     *
     * @param  \CommonBundle\Entity\User\Person\Academic $academic  The academic that should be checked
     * @param  boolean                                   $editAdmin Whether or not to check for permission to edit the admins of the list
     * @return boolean
     */
    public function canBeEditedBy(Academic $academic, $editAdmin = false)
    {
        foreach ($academic->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor')
                return true;

            foreach ($this->adminRoles as $adminRole) {
                if ($adminRole->getRole() == $role) {
                    if ($editAdmin && !$adminRole->canEditAdmin())
                        return false;

                    return true;
                }
            }
        }

        foreach ($this->admins as $admin) {
            if ($admin->getAcademic() == $academic) {
                if ($editAdmin && !$admin->canEditAdmin())
                    return false;

                return true;
            }
        }
    }
}
