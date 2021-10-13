<?php

namespace MailBundle\Entity\MailingList\Entry;

use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\MailingList as MailingListEntity;

/**
 * This is the entity for the list entry of a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry\MailingList")
 * @ORM\Table(name="mail_lists_entries_lists")
 */
class MailingList extends \MailBundle\Entity\MailingList\Entry
{
    /**
     * @var MailingListEntity The list associated with this entry
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList", cascade={"persist"})
     * @ORM\JoinColumn(name="entry", referencedColumnName="id", nullable=false)
     */
    private $entry;

    /**
     * @return MailingListEntity
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param  MailingListEntity $entry
     * @return self
     */
    public function setEntry(MailingListEntity $entry)
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->entry->getName();
    }
}
