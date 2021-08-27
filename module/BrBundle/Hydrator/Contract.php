<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Contract\Entry as EntryEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Contract data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Contract extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('title', 'payment_details', 'payment_days', 'discount_text', 'auto_discount_text');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a contract');
        }

        $newVersionNb = 0;

        foreach ($object->getEntries() as $entry) {
            if ($entry->getVersion() == $object->getVersion()) {
                $newVersionNb = $entry->getVersion() + 1;
                $newEntry = new EntryEntity(
                    $object,
                    $entry->getOrderEntry(),
                    $entry->getPosition(),
                    $newVersionNb
                );

                $newEntry->setContractText($data['entry_' . $entry->getId().'_nl'], 'nl');
                $newEntry->setContractText($data['entry_' . $entry->getId().'_en'], 'en');

                $this->getEntityManager()->persist($newEntry);
            }
        }

        $object->setVersion($newVersionNb);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        foreach ($object->getEntries() as $entry) {
            $data['entry_' . $entry->getId().'_nl'] = $entry->getContractText('nl', false);
            $data['entry_' . $entry->getId().'_en'] = $entry->getContractText('en', false);
        }

        return $data;
    }
}
