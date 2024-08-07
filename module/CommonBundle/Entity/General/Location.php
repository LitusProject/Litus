<?php

namespace CommonBundle\Entity\General;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a geographic location associated with an address.
 *
 * We use Google's API to convert addresses; more information can be found
 * here: https://developers.google.com/maps/documentation/geocoding/.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Location")
 * @ORM\Table(name="general_locations")
 */
class Location
{
    /**
     * @var integer The ID of the location
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The location's name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \CommonBundle\Entity\General\Address The address associated with the location
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The latitude coordinate
     *
     * @ORM\Column(type="string", length=12)
     */
    private $latitude;

    /**
     * @var string The longitude coordinate
     *
     * @ORM\Column(type="string", length=12)
     */
    private $longitude;

    /**
     * @var boolean Whether or not the location is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->active = true;
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

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param  Address $address
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param  string $latitude
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param  string $longitude
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }
}
