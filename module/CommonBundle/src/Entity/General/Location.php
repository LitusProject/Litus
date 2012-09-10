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
    public function __construct(EntityManager $entityManager, $name, Address $address)
    {
        $this->name = $name;
        $this->address = $address;
        $this->active = true;

        $this->_updateGeocode($entityManager);
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
    public function setAddress(EntityManager $entityManager, Address $address)
    {
        $this->address = $address;
        $this->_updateGeocode($entityManager);

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
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
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

    /**
     * Sets the latitude and longitude based on the results returned by
     * the specified geocoding API.
     *
     * @param  \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @return void
     */
    private function _updateGeocode(EntityManager $entityManager)
    {
        $geocodingUrl = $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.geocoding_api_url');

        $client = new Client(
            $geocodingUrl . (substr($geocodingUrl, -1) == '/' ? 'json' : '/json')
        );

        $client->setParameterGet(
            array(
                'sensor'  => 'false',
                'address' => urlencode(
                    $this->address->getStreet() . ' ' . $this->address->getNumber() . ', '
                        . $this->address->getPostal() . ' ' . $this->address->getCity() . ', '
                        . $this->address->getCountry()
                )
            )
        );

        $response = json_decode($client->send()->getBody());

        if ('OK' != $response->status)
            throw new \RuntimeException('Failed to correctly determine geocoding information');

        if (count($response->results) > 1)
            throw new \RuntimeException('The geocoding information found was ambiguous');

        $this->latitude = $response->results[0]->geometry->location->lat;
        $this->longitude = $response->results[0]->geometry->location->lng;
    }
}
