<?php

namespace BrBundle\Hydrator;

use CommonBundle\Component\Hydrator\Hydrator;

/**
 * This hydrator hydrates/extracts StudentCompanyMatch data.
 *
 * @author Robbe Serry <robbe.Serry@vtk.be>
 */
class StudentCompanyMatch extends Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new StudentCompanyMatch();
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();
        $data['categories'] = $object->getCategoriesAsString();

        return $data;
    }
}
