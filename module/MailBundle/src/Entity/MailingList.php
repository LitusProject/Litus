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
 * This is the entity for a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList")
 * @ORM\Table(name="mail.lists")
 */
class MailingList
{
    /**
     * @var int The list's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this list
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Creates a new list with the given name
     *
     * @param $name The name for this list
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name The name
     * @return \MailBundle\Entity\MailingList
     */
    public function setName($name) {
        $this->name = strtolower($name);
        return $this;
    }
}