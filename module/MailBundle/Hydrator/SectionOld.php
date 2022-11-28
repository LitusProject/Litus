<?php

namespace MailBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts section data.
 */
class Section extends \CommonBundle\Component\Hydrator\Hydrator
{


    protected function doHydrate(array $data, $object = null)
    {
        /**
         * @static @var string[] Key attributes to hydrate using the standard method.
         */
        static $stdKeys = array(
            'name', 'attribute'
        );

        if ($object === null) {
            throw new InvalidObjectException('Cannot create a sale article');
        }

        $this->stdHydrate($data, $object, $stdKeys);

        $name = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Section')
            ->findOneById($data['name']);
        $attribute = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Section')
            ->findOneById($data['attribute']);

        $object->setName($name);
        $object->setAttribute($attribute);

        return $object;
    }

    protected function doExtract($object = null)
    {
        /**
         * @static @var string[] Key attributes to hydrate using the standard method.
         */
        static $stdKeys = array(
            'name', 'attribute'
        );

        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, $stdKeys);

        $data['name'] = $object->getName();
        $data['attribute'] = $object->getAttribute();

        return $data;
    }
}