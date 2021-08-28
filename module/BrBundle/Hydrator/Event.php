<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Event as EventEntity;

/**
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title',
        'description_for_students',
        'description_for_companies',
        'nb_companies',
        'nb_students',
        'visible_for_companies',
        'visible_for_students',
        'location',
        'audience'
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new EventEntity($this->getPersonEntity());
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        return $object;
    }
}
