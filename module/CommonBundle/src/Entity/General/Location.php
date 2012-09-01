<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a geographic location associated with an address
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
     * @var string The address associated with the location
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
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
     * @param \CommonBundle\Entity\General\Address $address
     * @param string $latitude
     * @param string $longitude
     */
    public function __construct(Address $address, $latitude, $longitude)
    {
        $this->address = $address;
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
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
