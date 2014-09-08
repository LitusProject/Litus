<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Address as AddressEntity;

class PrimaryAddress extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'CommonBundle\Entity\General\Address';

    private static $std_keys = array(
        'number', 'mailbox',
    );

    private static $other_keys = array(
        'street', 'postal', 'city',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array(
                'country' => 'BE',
                'city'    => '',
            );
        }

        $city = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findOneByName($object->getCity());

        $data = $this->stdExtract($object, self::$std_keys);

        if (null !== $city) {
            $data['city'] = $city->getId();

            $street = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\Street')
                ->findOneByCityAndName($city, $object->getStreet());

            $data['street'][$city->getId()] = $street ? $street->getId() : 0;
         } else {
            $data['city'] = 'other';
            $data['other'] = $this->stdExtract($object, self::$other_keys);
        }

        $data['country'] = $object->getCountry();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new AddressEntity();
        }

        $object->setCountry('BE');

        if ($data['city'] === 'other') {
            $this->stdHydrate($data['other'], $object, self::$other_keys);
        } else {
            $city = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneById($data['city']);

            $street = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\Street')
                ->findOneById($data['street'][$data['city']]);

            $object->setCity($city->getName())
                ->setPostal($city->getPostal())
                ->setStreet($street !== null ? $street->getName() : '');
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}
