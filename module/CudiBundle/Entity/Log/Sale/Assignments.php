<?php

namespace CudiBundle\Entity\Log\Sale;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sale\Assignments")
 * @ORM\Table(name="cudi_log_sales_assignments")
 */
class Assignments extends \CudiBundle\Entity\Log
{
    /**
     * @param Person $person
     * @param array  $assignments
     */
    public function __construct(Person $person, $assignments)
    {
        parent::__construct($person, serialize($assignments));
    }

    /**
     * @return array
     */
    public function getAssigments()
    {
        return unserialize($this->getText());
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return count(unserialize($this->getText()));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'assignments';
    }
}
