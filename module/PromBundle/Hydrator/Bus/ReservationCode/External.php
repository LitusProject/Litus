<?php

namespace PromBundle\Hydrator\Bus\ReservationCode;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * @author Matthias Swiggers <matthias.swiggers@studentit.be>
 */
class External extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('email', 'first_name', 'last_name');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
