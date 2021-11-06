<?php

namespace SecretaryBundle\Hydrator\WorkingGroup;

class Academic extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data['person']['id'] = $object->getAcademic()->getId();
        $data['person']['value'] = $object->getAcademic()->getFullName() . ' - ' . $object->getAcademic()->getUniversityIdentification();

        return $data;
    }
}
