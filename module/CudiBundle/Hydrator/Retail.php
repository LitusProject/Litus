<?php

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Retail as RetailEntity;

class Retail extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('comment', 'anonymous');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['price'] = $object->getPrice();
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $article = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($data['article']['id']);

            $owner = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['owner']['id']);

            $object = new RetailEntity($article, $owner);
        }

        $object->setPrice($data['price'] * 100);
        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
