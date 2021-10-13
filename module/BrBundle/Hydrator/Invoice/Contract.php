<?php

namespace BrBundle\Hydrator\Invoice;

use BrBundle\Entity\Invoice\Entry as EntryEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Invoice data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Contract extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('vat_context', 'company_reference', 'tax_free', 'discount_text', 'auto_discount_text', 'eu');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create an invoice');
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

                $newEntry->setInvoiceText($data['entry_' . $entry->getId()]);

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
            $data['entry_' . $entry->getId()] = $entry->getInvoiceText();
        }

        return $data;
    }
}
