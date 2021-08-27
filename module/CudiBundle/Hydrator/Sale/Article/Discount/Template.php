<?php

namespace CudiBundle\Hydrator\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article\Discount\Template as TemplateEntity;

class Template extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name', 'method', 'rounding', 'type',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['organization'] = $object->getOrganization() === null ? '0' : $object->getOrganization()->getId();
        $data['apply_once'] = $object->applyOnce();
        $data['value'] = number_format($object->getValue() / 100.0, 2);

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new TemplateEntity();
        }

        $organization = null;
        if (isset($data['organization']) && $data['organization'] != 0) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneById($data['organization']);
        }

        $object->setOrganization($organization);

        return $this->stdHydrate($data, $object, array(self::$stdKeys, array('apply_once', 'value')));
    }
}
