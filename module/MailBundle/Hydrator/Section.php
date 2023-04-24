<?php

namespace MailBundle\Hydrator;

use MailBundle\Entity\Section as SectionEntity;

class Section extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $array, $object = null)
    {
        if ($object === null) {
            $object = new SectionEntity();
        }

        $object->setName($array['name']);
        $group = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Section\Group')
            ->findOneById($array['section_group']);
        if ($group !== null) {
            $object->setGroup($group);
        }
        $object->setDefaultValue(isset($array['default_value']));
        $object->setAttribute($array['attribute']);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();
        $data['name'] = $object->getName();
        $data['section_group'] = $object->getGroup() !== null ? $object->getGroup()->getId() : -1;
        $data['default_value'] = $object->getDefaultValue();
        $data['attribute'] = $object->getAttribute();

        return $data;
    }
}