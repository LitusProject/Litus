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

namespace CommonBundle\Entity\General;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    Zend\Http\Client;

/**
 * This class represents a geographic location associated with an address.
 *
 * We use Google's API to convert addresses; more information can be found
 * here: https://developers.google.com/maps/documentation/geocoding/.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Location")
 * @ORM\Table(name="general.locations")
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
     * @var string The address associated with the location
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
     * @var bool Whether or not the category is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $name
     * @param \CommonBundle\Entity\General\Address $address
     */
    public function __construct($name, Address $address, $latitude, $longitude)
    {
        $this->name = $name;
        $this->address = $address;
        $this->active = true;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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
     * @param string $name
     * @return \CommonBundle\Entity\General\Location
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\General\Address $address
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
     * @param string $latitude
     * @return CommonBundle\Entity\General\Location
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
     * @param string $longitude
     * @return CommonBundle\Entity\General\Location
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
