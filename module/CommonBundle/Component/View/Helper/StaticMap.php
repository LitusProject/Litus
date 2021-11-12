<?php

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Entity\General\Location;
use Doctrine\ORM\EntityManager;

/**
 * This view helper can be used to create an URL of an image that displays
 * a map on which a given location is shown.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class StaticMap extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager;

    /**
     * @param  EntityManager $entityManager The EntityManager instance
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param  Location $location The location that should be verified
     * @param  string   $size     The image's size
     * @param  string   $color    The hex value for the color of the marker on the image
     * @return string
     */
    public function __invoke(Location $location, $size, $color)
    {
        if ($this->entityManager === null) {
            throw new Exception\RuntimeException('No EntityManager instance was provided');
        }

        $staticMapsUrl = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.static_maps_api_url');

        if (substr($staticMapsUrl, -1) == '/') {
            $staticMapsUrl = substr($staticMapsUrl, 0, -1);
        }

        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();

        return $staticMapsUrl . '?center=' . $coordinates . '&zoom=16&size=' . $size . '&markers=color:0x' . $color . '%7C' . $coordinates . '&sensor=false';
    }
}
