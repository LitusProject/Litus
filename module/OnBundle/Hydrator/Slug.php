<?php

namespace OnBundle\Hydrator;

use OnBundle\Entity\Slug as SlugEntity;

/**
 * This hydrator hydrates/extracts slug data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Slug extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'OnBundle\Entity\Slug';

    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('name', 'url', 'active');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SlugEntity($this->getPersonEntity());
        }

        if ($data['name'] == '') {
            do {
                $name = $this->createRandomName();
                $found = $this->getEntityManager()
                    ->getRepository('OnBundle\Entity\Slug')
                    ->findOneByName($name);
            } while (isset($found));

            $data['name'] = $name;
        } else {
            $data['name'] = strtolower($data['name']);
        }

        if (isset($data['expiration_date'])) {
            $object->setExpirationDate(self::loadDate($data['expiration_date']));
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }

    private function createRandomName()
    {
        $characters = 'abcdefghijklmnopqrstuwxyz0123456789';

        $name = array();
        for ($i = 0; $i < 8; $i++) {
            $name[$i] = $characters[rand(0, strlen($characters) - 1)];
        }

        return implode('', $name);
    }
}
