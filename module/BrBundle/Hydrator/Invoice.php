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

namespace BrBundle\Hydrator;


use BrBundle\Entity\Invoice\InvoiceEntry as InvoiceEntryEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Invoice data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Invoice extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create an invoice');
        }

        $object->setVATContext($data['VATContext']);

        $newVersionNb = 0;

        foreach ($object->getEntries() as $entry) {
            if ($entry->getVersion() == $object->getVersion()) {
                $newVersionNb = $entry->getVersion() + 1;
                $newInvoiceEntry = new InvoiceEntryEntity($object, $entry->getOrderEntry(), $entry->getPosition(), $newVersionNb);

                $this->getEntityManager()->persist($newInvoiceEntry);

                $newInvoiceEntry->setInvoiceText($data['entry_' . $entry->getId()]);
            }
        }

        $object->setVersion($newVersionNb);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        $data['VATContext'] = $object->getVATContext();

        foreach ($object->getEntries() as $entry) {
            $data['entry_' . $entry->getId()] = $entry->getInvoiceText();
        }

        return $data;
    }
}
