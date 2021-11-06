<?php

namespace CudiBundle\Entity\Sale\Session\Restriction;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction\Name")
 * @ORM\Table(name="cudi_sale_sessions_restrictions_name")
 */
class Name extends \CudiBundle\Entity\Sale\Session\Restriction
{
    /**
     * @var string The start value of restriction
     *
     * @ORM\Column(type="string", name="start_value")
     */
    private $startValue;

    /**
     * @var string The end value of restriction
     *
     * @ORM\Column(type="string", name="end_value")
     */
    private $endValue;

    /**
     * @param Session $session
     */
    public function __construct(Session $session, $startValue, $endValue)
    {
        parent::__construct($session);

        $this->startValue = strtolower($startValue);
        $this->endValue = strtolower($endValue);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'name';
    }

    /**
     * @return string
     */
    public function getStartValue()
    {
        return $this->startValue;
    }

    /**
     * @return string
     */
    public function getEndValue()
    {
        return $this->endValue;
    }

    /**
     * @return string
     */
    public function getReadableValue()
    {
        return $this->startValue . ' - ' . $this->endValue;
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        return !(strtolower(substr($person->getLastName(), 0, strlen($this->startValue))) < $this->startValue
                || strtolower(substr($person->getLastName(), 0, strlen($this->endValue))) > $this->endValue);
    }
}
