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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Hydrator\MailingList;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use MailBundle\Entity\MailingList\AdminMap as ListAdminEntity;
use MailBundle\Entity\MailingList\Named as NamedEntity;

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
        if (null !== $object) {
            throw new InvalidObjectException();
        }

        $list = new NamedEntity($data['name']);
        $this->getEntityManager()->persist($list);

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person']['id']);

        $admin = new ListAdminEntity($list, $academic, true);
        $this->getEntityManager()->persist($admin);

        return $list;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
