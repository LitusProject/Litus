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

namespace MailBundle\Entity\MailingList\Entry\Person;

use Doctrine\ORM\Mapping as ORM,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for the list entry of an academic.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry\Person\Academic")
 * @ORM\Table(name="mail.lists_entries_people_academic")
 */
class Academic extends \MailBundle\Entity\MailingList\Entry\Person
{
    /**
     * @var CommonBundle\Entity\User\Person\Academic The academic associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new list entry for the given list with the given academic?
     *
     * @param \MailBundle\Entity\MailingList $list The list for this entry
     * @param \CommonBundle\Entity\User\Person\Academic $academic The academic to add
     */
    public function __construct(MailingList $list, \CommonBundle\Entity\User\Person\Academic $academic)
    {
        parent::__construct($list);
        $this->academic = $academic;
    }

    /**
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function getAcademic() {
        return $this->academic;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }
}
