<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Tombola extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('runner_identification');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}
