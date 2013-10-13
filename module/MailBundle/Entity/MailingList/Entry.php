<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for a list entry.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry")
 * @ORM\Table(name="mail.lists_entries")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="MailBundle\Entity\MailingList\Entry\Person\Academic",
 *      "external"="MailBundle\Entity\MailingList\Entry\Person\External",
 *      "list"="MailBundle\Entity\MailingList\Entry\MailingList"
 * })
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
     * @var \MailBundle\Entity\MailingList The list associated with this entry
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList", cascade={"persist"})
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * Creates a new list entry for the given list.
     *
     * @param \MailBundle\Entity\MailingList $list The list for this entry
     */
    public function __construct(MailingList $list)
    {
        $this->list = $list;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \MailBundle\Entity\MailingList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return string
     */
    abstract public function getEmailAddress();
}
