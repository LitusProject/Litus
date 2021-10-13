<?php

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
        if ($object !== null) {
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
