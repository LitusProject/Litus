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
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList")
 * @ORM\Table(name="mail.list")
 */
class MailingList
{

    /**
     * @var The list's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this list.
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Creates a new list with the given name
     *
     * @param $name The name for this list.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return The id of this list.
     */
    public function getId() {
        return $id;
    }

    /**
     * @return The name of this list.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of this list.
     *
     * @param $name The name.
     * @return This list.
     */
    public function setName($name) {
        $this->name = name;
        return $this;
    }
}