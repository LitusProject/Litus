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
namespace MailBundle\Entity\Entry;

use CommonBundle\Entity\Users\People\Academic as AcademicPerson,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Entry\External")
 * @ORM\Table(name="mail.entry_academic")
 */
class Academic extends \MailBundle\Entity\Entry
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
     * @var CommonBundle\Entity\Users\People\Academic The academic associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * Creates a new list entry for the given list with the given academic.
     *
     * @param MailBundle\Entity\MailingList $list The list for this entry.
     * @param CommonBundle\Entity\Users\People\Academic $academic The academic to add.
     */
    public function __construct(MailingList $list, AcademicPerson $academic)
    {
        parent::__construct($list);
        $this->mail = $mail;
    }

    /**
     * @return The id of this entry.
     */
    public function getId() {
        return $id;
    }

    /**
     * @return The academic associated with this entry.
     */
    public function getAcademic() {
        return $this->academic;
    }

    /**
     * @return The mail address of this entry.
     */
    public function getMailAddress()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return The first name of this entry.
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return The last name of this entry.
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }
}