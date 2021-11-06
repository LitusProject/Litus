<?php

namespace MailBundle\Hydrator\MailingList\Entry;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts academic entry data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class MailingList extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        $entry = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Named')
            ->findOneById($data['entry']);

        $object->setEntry($entry);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
