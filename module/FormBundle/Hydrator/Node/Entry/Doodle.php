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

namespace FormBundle\Hydrator\Node\Entry;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    FormBundle\Entity\Entry as FieldEntryEntity;

class Doodle extends \FormBundle\Hydrator\Node\Entry
{
    protected $entity = 'FormBundle\Entity\Node\Entry';

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a entry');
        }

        if (isset($data['guest_form'])) {
            $guestData = $data['guest_form'];
        } else {
            $guestData = $data;
        }

        if (isset($data['fields_form'])) {
            $fieldData = $data['fields_form'];
        } else {
            $fieldData = $data;
        }

        $guestInfo = $object->getGuestInfo();
        if (null !== $guestInfo && isset($guestData['first_name'])) {
            $guestInfo->setFirstName($guestData['first_name'])
                ->setLastName($guestData['last_name'])
                ->setEmail($guestData['email']);
        }

        foreach ($object->getFieldEntries() as $fieldEntry) {
            $object->removeFieldEntry($fieldEntry);
        }
        $this->getEntityManager()->flush();

        foreach ($object->getForm()->getFields() as $field) {
            if (isset($fieldData['field-' . $field->getId()]) && $fieldData['field-' . $field->getId()]) {
                $fieldEntry = new FieldEntryEntity($object, $field, '1');
                $object->addFieldEntry($fieldEntry);

                if (!$object->getForm()->isMultiple()) {
                    break;
                }
            }
        }

        return $object;
    }
}
