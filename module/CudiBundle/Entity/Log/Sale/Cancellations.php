<?php

namespace CudiBundle\Entity\Log\Sale;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Sale\Cancellations")
 * @ORM\Table(name="cudi_log_sales_cancellations")
 */
class Cancellations extends \CudiBundle\Entity\Log
{
    /**
     * @param Person $person
     * @param array  $cancellations
     */
    public function __construct(Person $person, $cancellations)
    {
        parent::__construct($person, serialize($cancellations));
    }

    /**
     * @return array
     */
    public function getCancellations()
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
        return 'cancellations';
    }
}
