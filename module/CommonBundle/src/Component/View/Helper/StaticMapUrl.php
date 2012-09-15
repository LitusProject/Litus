<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Entity\General\Location,
    Doctrine\ORM\EntityManager;

/**
 * This view helper can be used to create an URL of an image that displays
 * a map on which a given location is shown.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class StaticMapUrl extends \Zend\View\Helper\AbstractHelper
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \CommonBundle\Component\Acl\Driver\HasAccess $driver The driver object
     * @return \CommonBundle\Component\View\Helper\StaticMap
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Location $location The location that should be verified
     * @param string $size The image's size
     * @param string $color The hex value for the color of the marker on the image
     * @return bool
     */
    public function __invoke(Location $location, $size, $color)
    {
        if (null === $this->_entityManager)
            throw new Exception\RuntimeException('No EntityManager instance was provided');

        $staticMapsUrl = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.static_maps_api_url');

        if (substr($staticMapsUrl, -1) == '/')
            $staticMapsUrl = substr($staticMapsUrl, 0, -1);

        $coordinates = $location->getLatitude() . ',' . $location->getLongitude();

        return $staticMapsUrl . '?center=' . $coordinates . '&zoom=16&size=' . $size . '&markers=color:0x' . $color . '%7C' . $coordinates . '&sensor=false';
    }
}
