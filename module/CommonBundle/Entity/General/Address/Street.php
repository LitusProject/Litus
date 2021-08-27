<?php

namespace CommonBundle\Entity\General\Address;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a address entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Address\Street")
 * @ORM\Table(
 *     name="general_addresses_streets",
 *     indexes={@ORM\Index(name="general_addresses_streets_name", columns={"name"})}
 * )
 */
class Street
{
    /**
     * @var integer The ID of the street
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The register number
     *
     * @ORM\Column(name="register_number", type="smallint")
     */
    private $registerNumber;

    /**
     * @var string The street name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var City The city
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Address\City", inversedBy="streets")
     * @ORM\JoinColumn(name="city", referencedColumnName="id")
     */
    private $city;

    /**
     * @param City    $city
     * @param integer $registerNumber
     * @param string  $name
     */
    public function __construct(City $city, $registerNumber, $name)
    {
        $this->city = $city;
        $this->registerNumber = $registerNumber;
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
     * @return integer
     */
    public function getRegisterNumber()
    {
        return $this->registerNumber;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }
}
