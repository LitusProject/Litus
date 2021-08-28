<?php

namespace MailBundle\Hydrator\MailingList;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts adminrole data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AdminRoleMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        $role = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findOneByName($data['role']);

        $object->setRole($role)
            ->setEditAdmin($data['edit_admin']);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
