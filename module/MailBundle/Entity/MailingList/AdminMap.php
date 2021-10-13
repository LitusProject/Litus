<?php

namespace MailBundle\Entity\MailingList;

use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\MailingList;

/**
 * This entity maps admins to mailing lists.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\AdminMap")
 * @ORM\Table(name="mail_lists_admins_map")
 */
class AdminMap
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
     * @var Academic The academic of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var boolean The flag whether the academic is allowed to edit the list of admins of the list too.
     *
     * @ORM\Column(name="edit_admin", type="boolean")
     */
    private $editAdmin;

    /**
     * @param MailingList   $list      The list of the mapping
     * @param Academic|null $academic  The academic of the mapping
     * @param boolean       $editAdmin The flag whether the academic is allowed to edit the list of admins of the list too.
     */
    public function __construct(MailingList $list, Academic $academic = null, $editAdmin = false)
    {
        $this->list = $list;
        $this->academic = $academic;
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
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @return boolean
     */
    public function canEditAdmin()
    {
        return $this->editAdmin;
    }

    /**
     * @param  boolean $editAdmin
     * @return self
     */
    public function setEditAdmin($editAdmin)
    {
        $this->editAdmin = $editAdmin;

        return $this;
    }
}
