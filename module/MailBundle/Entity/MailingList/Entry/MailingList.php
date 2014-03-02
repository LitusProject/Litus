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

namespace MailBundle\Entity\MailingList\Entry;

use MailBundle\Entity\MailingList as MailingListEntity,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the list entry of a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry\MailingList")
 * @ORM\Table(name="mail.lists_entries_lists")
 */
class MailingList extends \MailBundle\Entity\MailingList\Entry
{
    /**
     * @var MailBundle\Entity\MailingList The list associated with this entry
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList", cascade={"persist"})
     * @ORM\JoinColumn(name="entry", referencedColumnName="id", nullable=false)
     */
    private $entry;

    /**
     * Creates a new list entry for the given list with the given list.
     *
     * @param \MailBundle\Entity\MailingList $list  The list for this entry
     * @param \MailBundle\Entity\MailingList $entry The list associated with this entry
     */
    public function __construct(MailingListEntity $list, MailingListEntity $entry)
    {
        parent::__construct($list);
        $this->entry = $entry;
    }

    /**
     * @return \MailBundle\Entity\MailingList
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->entry->getName();
    }
}
