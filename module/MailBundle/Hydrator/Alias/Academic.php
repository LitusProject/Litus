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
        if (null === $object) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']);

            $object = new AcademicEntity($data['alias'], $academic);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['person']['id'] = $object->getAcademic()->getId();
        $data['person']['value'] = $object->getAcademic()->getFullName() . ' - ' . $object->getAcademic()->getUniversityIdentification();

        return $data;
    }
}
