<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Communication as CommunicationEntity;

/**
 * This hydrator hydrates/extracts Communication data.
 *
 * @autho Stan Cardinaels <stan.cardinaels@vtk.be>
 */
class Communication extends \CommonBundle\Component\Hydrator\Hydrator
{

    private static $stdKeys = array('option', 'audience');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CommunicationEntity($this->getPersonEntity());
        }

        if (isset($data['companyId'])) {
            $object->setCompany(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($data['companyId'])
            );
        }

        if (isset($data['date'])) {
            $object->setDate(self::loadDate($data['date']));
        }


        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['company'] = $object->getCompany() !== null ? $object->getCompany()->getName() : -1;

        $data['date'] = $object->getDate()->format('d/m/Y H:i');

        return $data;
    }
}
