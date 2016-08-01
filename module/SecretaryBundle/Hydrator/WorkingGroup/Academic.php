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

namespace SecretaryBundle\Hydrator\WorkingGroup;

class Academic extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data['person']['id'] = $object->getAcademic()->getId();
        $data['person']['value'] = $object->getAcademic()->getFullName() . ' - ' . $object->getAcademic()->getUniversityIdentification();

        return $data;
    }
}
