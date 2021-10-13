<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\Driver as DriverEntity;

class Driver extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('color');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $years = array();
        foreach ($object->getYears() as $year) {
            $years[] = $year->getId();
        }
        $data['years'] = $years;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneByPerson($data['person']['id']);

            if ($object === null) {
                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($data['person']['id']);

                $object = new DriverEntity($person);
            }
        }

        $object->setRemoved(false);

        $years = array();
        $repository = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear');
        foreach ($data['years'] as $year) {
            $years[] = $repository->findOneById($year);
        }
        $object->setYears($years);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
