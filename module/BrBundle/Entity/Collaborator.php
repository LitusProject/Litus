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

namespace BrBundle\Entity;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is a person that is a collaborator of corporate relations.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Collaborator")
 * @ORM\Table(name="br.collaborator")
 */
class Collaborator
{
    /**
     * @var integer The company's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The contract accompanying this order
     *
     * @ORM\OneToOne(targetEntity="\CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var int Integer that resembles the personal number of the person.
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var bool True if the current person is an active member of corporate relations.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->activate();
    }

     /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  int  $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return self
     */
    public function activate()
    {
        $this->active = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return self
     */
    public function retire()
    {
        $this->active = false;

        return $this;
    }

    /**
     * @return self
     */
    public function rehire()
    {
        $this->active = true;

        return $this;
    }
}
