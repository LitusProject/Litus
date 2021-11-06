<?php

namespace MailBundle\Hydrator\MailingList;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts adminmap data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AdminMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person']['id']);

        $object->setAcademic($academic)
            ->setEditAdmin($data['edit_admin']);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
