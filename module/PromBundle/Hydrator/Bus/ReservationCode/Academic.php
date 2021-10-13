<?php

namespace PromBundle\Hydrator\Bus\ReservationCode;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 *
 * @author Matthias Swiggers <matthias.swiggers@studentit.be>
 */
class Academic extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person']['id']);

        $object->setAcademic($academic);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
