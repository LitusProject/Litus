<?php

namespace CommonBundle\Entity\General\Bank;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an appliance thtat is when someone pays by card.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\BankDevice")
 * @ORM\Table(name="general_bank_bank_devices")
 */
class BankDevice
{
    /**
     * @var integer The device's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The device's name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param string $name The device's name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
