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
     * @var string The company's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Collaborator The contract accompanying this order
     *
     * @ORM\OneToOne(
     *      targetEntity="\CommonBundle\Entity\User\Person"
     * )
     */
    private $person;

    /**
     * @var Integer that resembles the personal number of the person.
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
     */
    public function __construct(Person $person, $number)
    {
        $this->_setPerson($person);
        $this->setNumber($number);
        $this->activate();
    }

     /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    private function _setPerson(Person $person)
    {
        $this->person = $person;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function activate()
    {
        $this->active = true;
    }

    public function retire()
    {
        $this->active = false;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function isActive()
    {
        return $this->active;
    }

}
