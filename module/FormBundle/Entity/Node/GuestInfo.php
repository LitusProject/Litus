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

namespace FormBundle\Entity\Node;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\GuestInfo")
 * @ORM\Table(name="forms.guests_info")
 */
class GuestInfo
{
    /**
     * @var int The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The session id of this guest info item
     *
     * @ORM\Column(type="string", length=32, unique=true, nullable=true)
     */
    private $sessionId;

    /**
     * @var string The first name of this guest
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string The last name of this guest
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string The email address of this guest
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     */
    public function __construct(EntityManager $entityManager, $firstName, $lastName, $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;

        do {
            $sessionId = md5(uniqid(rand(), true));

            $guestInfo = $entityManager->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($sessionId);
        } while($guestInfo !== null);

        $this->sessionId = $sessionId;

        $this->renew();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string The full name
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return string email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \FormBundle\Entity\Node\GuestInfo
     */
    public function renew()
    {
        setcookie(
            'LITUS_form',
            $this->sessionId,
            time() + (60*60*24*25),
            '/',
            str_replace(array('www.', ','), '', $_SERVER['SERVER_NAME'])
        );
        return $this;
    }
}
