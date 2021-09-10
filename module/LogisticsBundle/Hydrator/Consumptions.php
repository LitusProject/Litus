<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\Consumptions as ConsumptionsEntity;

class Consumptions extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('option', 'audience');

    protected function doHydrate(array $array, $object = null)
    {
        if ($object === null) {
            $object = new ConsumptionsEntity();
        }

        if (isset($array['academic'])) {
            $object->setAcademic(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($array['academic'])
            );
        }

        return $this->stdHydrate($array, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['academic'] = $object->getAcademic() !== null ? $object->getAcademic()->getUniversityIdentification() : -1;

        $data['numberOfConsumptions'] = $object->getConsumptions();

        return $data;
    }
}