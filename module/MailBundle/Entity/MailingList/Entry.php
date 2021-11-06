<?php

namespace MailBundle\Entity\MailingList;

use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\MailingList;

/**
 * This is the entity for a list entry.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry")
 * @ORM\Table(name="mail_lists_entries")
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
     * @var integer The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var MailingList The list associated with this entry
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList", cascade={"persist"})
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * Creates a new list entry for the given list.
     *
     * @param MailingList $list The list for this entry
     */
    public function __construct(MailingList $list)
    {
        $this->list = $list;
    }

    /**
     * @return integer
     */
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
     * @return string
     */
    abstract public function getEmailAddress();
}
