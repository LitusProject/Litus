<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Entity\MailingList;

use CommonBundle\Entity\Users\People\Academic,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity maps admins to mailinglists.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\AdminMap")
 * @ORM\Table(name="mail.list_admins")
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
     * @var \MailBundle\Entity\MailingList The list of the mapping
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList")
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The academic of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
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
     * @param \MailBundle\Entity\MailingList
     * @param \CommonBundle\Entity\Users\People\Academic $academic
     * @param boolean $editAdmin
     */
    public function __construct($list, $academic, $editAdmin)
    {
        $this->academic = $academic;
        $this->list = $list;
        $this->editAdmin = $editAdmin;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return \MailBundle\Entity\MailingList
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic() {
        return $this->academic;
    }

    /**
     * @return boolean
     */
    public function isEditAdmin() {
        return $this->editAdmin;
    }
}
