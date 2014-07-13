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

namespace MailBundle\Hydrator\MailingList;

use MailBundle\Entity\MailingList\Named as NamedEntity,
    MailBundle\Entity\MailingList\AdminMap as ListAdminEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts mailinglist data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class MailingList extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null !== $object)
            throw new InvalidObjectException();

        $list = new NamedEntity($data['name']);
        $this->getEntityManager()->persist($list);

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person_id']);

        $admin = new ListAdminEntity($list, $academic, true);
        $this->getEntityManager()->persist($admin);

        return $list;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
