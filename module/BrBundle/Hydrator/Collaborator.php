<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Collaborator as CollaboratorEntity;

/**
 * This hydrator hydrates/extracts Collaborator data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Collaborator extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('number');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CollaboratorEntity(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($data['person']['id'])
            );
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
}
