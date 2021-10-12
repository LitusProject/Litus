<?php

namespace TicketBundle\Hydrator;

use TicketBundle\Entity\Consumptions as ConsumptionsEntity;

class Consumptions extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $array, $object = null)
    {
        if ($object === null) {
            $object = new ConsumptionsEntity();
        }

       $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($array['person']['id']);
        $object->setPerson($academic);

        $numberOfConsumptions = $array['number_of_consumptions'];
        $object->setConsumptions($numberOfConsumptions);
        $object->setUserName($academic->getUserName());
        $object->setFullName($academic->getFullName());

//        return $this->stdHydrate($array, $object);
        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
//        error_log(json_encode())
        $data['person']['id'] = $object->getPerson() !== null ? $object->getPerson()->getId() : -1;
        $data['person']['value'] = $object->getPerson() !== null ? $object->getFullName() . " - " . $object->getUserName() : -1;
        $data['number_of_consumptions'] = $object->getConsumptions();

//        $data = $this->stdExtract($object);
        return $data;
    }
}