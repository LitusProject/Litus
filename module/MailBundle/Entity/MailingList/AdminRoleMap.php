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

namespace MailBundle\Entity\MailingList;

use CommonBundle\Entity\Acl\Role,
    Doctrine\ORM\Mapping as ORM,
    MailBundle\Entity\MailingList;

/**
 * This entity maps admin roles to mailing lists.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\AdminRoleMap")
 * @ORM\Table(name="mail.lists_admin_roles")
 */
class AdminRoleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var MailingList The list of the mapping
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList")
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * @var Role The role of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="name")
     */
    private $role;

    /**
     * @var boolean The flag whether the members of the role are allowed to edit the list of admins of the list too.
     *
     * @ORM\Column(name="edit_admin", type="boolean")
     */
    private $editAdmin;

    /**
     * @param MailingList The list of the mapping
     * @param Role    $role      The role of the mapping
     * @param boolean $editAdmin The flag whether the members of the role are allowed to edit the list of admins of the list too.
     */
    public function __construct(MailingList $list, Role $role, $editAdmin)
    {
        $this->list = $list;
        $this->role = $role;
        $this->editAdmin = $editAdmin;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MailingList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return boolean
     */
    public function canEditAdmin()
    {
        return $this->editAdmin;
    }
}
