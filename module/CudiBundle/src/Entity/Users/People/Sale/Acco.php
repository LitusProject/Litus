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

namespace CudiBundle\Entity\Users\People\Sale;

use CommonBundle\Entity\Users\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an supplier person.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Users\People\Sale\Acco")
 * @ORM\Table(name="users.people_sale_acco")
 */
class Acco
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\Person The person associated with this entity
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean Flag whether this person has an acco card
     *
     * @ORM\Column(name="has_acco_card", type="boolean")
     */
    private $hasAccoCard;

    /**
     * @param \CommonBundle\Entity\Users\Person $person The person associated with this entity
     * @param boolean $hasAccoCard Flag whether this person has an acco card
     */
    public function __construct(Person $person, $hasAccoCard)
    {
        $this->person = $person;
        $this->hasAccoCard = $hasAccoCard;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function hasAccoCard()
    {
        return $this->hasAccoCard;
    }

    /**
     * @param boolean $hasAccoCard
     *
     * @return \CudiBundle\Entity\Users\People\Sale\Acco
     */
    public function setHasAccoCard($hasAccoCard)
    {
        $this->hasAccoCard = $hasAccoCard;
        return $this;
    }
}
