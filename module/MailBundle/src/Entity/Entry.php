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
namespace MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Entry")
 * @ORM\Table(name="mail.list_entry")
 */
abstract class Entry
{

    /**
     * @var The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var MailBundle\Entity\List The list associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\List", cascade={"persist"})
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * Creates a new list entry for the given list.
     *
     * @param MailBundle\Entity\List $list The list for this entry.
     */
    public function __construct(List $list)
    {
        $this->list = $list;
    }

    /**
     * @return The id of this entry.
     */
    public function getId() {
        return $id;
    }

    /**
     * @return The list of this entry.
     */
    public function getList()
    {
        return $this->list;
    }

    abstract public function getMailAddress();
}