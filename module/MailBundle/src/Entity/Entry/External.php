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
    Doctrine\Common\Collections\ArrayCollection,
    MailBundle\Entity\List;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Entry\External")
 * @ORM\Table(name="mail.entry_external")
 */
class External extends \MailBundle\Entity\Entry
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
     * @var string The email address of this entry.
     *
     * @ORM\Column(type="string")
     */
    private $mail;

    /**
     * Creates a new list entry for the given list with the given mail address.
     *
     * @param MailBundle\Entity\List $list The list for this entry.
     * @param string $mail The email address to add.
     */
    public function __construct(List $list, $mail)
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
     * @return The mail address of this entry.
     */
    public function getMailAddress()
    {
        return $this->mail;
    }
}