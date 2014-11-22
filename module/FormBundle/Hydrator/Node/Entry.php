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

namespace FormBundle\Hydrator\Node;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Entry extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array();

    protected function doHydrate(array $data, $object = null)
    {
        throw new InvalidObjectException('Cannot create an entry');
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        if ($object->getGuestInfo()) {
            $data['first_name'] = $object->getGuestInfo()->getFirstName();
            $data['last_name'] = $object->getGuestInfo()->getLastName();
            $data['email'] = $object->getGuestInfo()->getEmail();
        }

        foreach ($object->getFieldEntries() as $fieldEntry) {
            $data['field-' . $fieldEntry->getField()->getId()] = $fieldEntry->getValue();
        }

        return $data;
    }
}
