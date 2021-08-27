<?php

namespace CudiBundle\Entity\Sale\Session;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction")
 * @ORM\Table(name="cudi_sale_sessions_restrictions")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "name"="CudiBundle\Entity\Sale\Session\Restriction\Name",
 *      "study"="CudiBundle\Entity\Sale\Session\Restriction\Study",
 *      "year"="CudiBundle\Entity\Sale\Session\Restriction\Year"
 * })
 */
abstract class Restriction
{
    /**
     * @var integer The ID of the restriction
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Session The session of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session", inversedBy="restrictions")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return string
     */
    abstract public function getReadableValue();

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    abstract public function canSignIn(EntityManager $entityManager, Person $person);
}
