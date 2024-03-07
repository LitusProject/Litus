<?php

namespace BrBundle\Hydrator;

/**
 * This hydrator hydrates/extracts StudentCompanyMatch data.
 *
 * @author Robbe Serry <robbe.Serry@vtk.be>
 */
class StudentCompanyMatch extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array();

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new StudentCompanyMatch();
        }

        // TODO fix hydrate

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        // TODO fix extract
        return $this->stdExtract($object, self::$stdKeys);
    }
}
