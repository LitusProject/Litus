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

namespace MailBundle\Hydrator\MailingList\Entry\Person;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts academic entry data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Academic extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object)
            throw new InvalidObjectException();

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person_id']);

        $object->setAcademic($academic);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
