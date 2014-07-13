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

namespace MailBundle\Entity\MailingList\Entry\Person;

use Doctrine\ORM\Mapping as ORM,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for the list entry of an external person.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry\Person\External")
 * @ORM\Table(name="mail.lists_entries_people_external")
 */
class External extends \MailBundle\Entity\MailingList\Entry\Person
{
    /**
     * @var string The first name of this entry
     *
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string The last name of this entry
     *
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string The e-mail address of this entry
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * Creates a new list entry for the given list with the given mail address.
     *
     * @param MailingList $list      The list for this entry
     * @param string      $firstName The first name to add
     * @param string      $lastName  The last name to add
     * @param string      $email     The e-mail address to add
     */
    public function __construct(MailingList $list)
    {
        parent::__construct($list);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param  string   $firstName
     * @return External
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param  string   $lastName
     * @return External
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email;
    }

    /**
     * @param  string   $email
     * @return External
     */
    public function setEmailAddress($email)
    {
        $this->email = $email;

        return $this;
    }
}
