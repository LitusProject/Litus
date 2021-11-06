<?php

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Address as AddressEntity;

class PrimaryAddress extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'CommonBundle\Entity\General\Address';

    private static $stdKeys = array(
        'number', 'mailbox',
    );

    private static $otherKeys = array(
        'street', 'postal', 'city',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array(
                'country' => 'BE',
                'city'    => '',
            );
        }

        $city = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findOneByName($object->getCity());

        $data = $this->stdExtract($object, self::$stdKeys);

        if ($city !== null) {
            $data['city'] = $city->getId();

            $street = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\Street')
                ->findOneByCityAndName($city, $object->getStreet());

            $data['street']['street_' . $city->getId()] = $street ? $street->getId() : 0;
        } else {
            $data['city'] = 'other';
            $data['other'] = $this->stdExtract($object, self::$otherKeys);
        }

        $data['country'] = $object->getCountry();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new AddressEntity();
        }

        $object->setCountry('BE');

        if ($data['city'] === 'other') {
            $this->stdHydrate($data['other'], $object, self::$otherKeys);
        } else {
            $city = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneById($data['city']);

            $street = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\Street')
                ->findOneById($data['street']['street_' . $data['city']]);

            $object->setCity($city->getName())
                ->setPostal($city->getPostal())
                ->setStreet($street !== null ? $street->getName() : '');
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
