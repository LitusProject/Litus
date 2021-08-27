<?php

namespace MailBundle\Hydrator\Alias;

use MailBundle\Entity\Alias\Academic as AcademicEntity;

/**
 * This hydrator hydrates/extracts alias data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Academic extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('alias');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']);

            $object = new AcademicEntity($data['alias'], $academic);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['person']['id'] = $object->getAcademic()->getId();
        $data['person']['value'] = $object->getAcademic()->getFullName() . ' - ' . $object->getAcademic()->getUniversityIdentification();

        return $data;
    }
}
