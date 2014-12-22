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


use BrBundle\Entity\Contract\ContractEntry as ContractEntryEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

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
    private static $std_keys = array('title', 'invoice_nb');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a contract');
        }

        $newVersionNb = 0;

        foreach ($object->getEntries() as $entry) {
            if ($entry->getVersion() == $object->getVersion()) {
                $newVersionNb = $entry->getVersion() + 1;
                $newContractEntry = new ContractEntryEntity($object, $entry->getOrderEntry(), $entry->getPosition(), $newVersionNb);

                $this->getEntityManager()->persist($newContractEntry);

                $newContractEntry->setContractText($data['entry_' . $entry->getId()]);
            }
        }

        $object->setVersion($newVersionNb);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        foreach ($object->getEntries() as $entry) {
            $data['entry_' . $entry->getId()] = $entry->getContractText();
        }

        return $data;
    }
}
