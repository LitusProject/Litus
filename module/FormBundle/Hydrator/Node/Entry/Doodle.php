<?php

namespace FormBundle\Hydrator\Node\Entry;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use FormBundle\Entity\Entry as FieldEntryEntity;

class Doodle extends \FormBundle\Hydrator\Node\Entry
{
    protected $entity = 'FormBundle\Entity\Node\Entry';

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
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
        if ($guestInfo !== null && isset($guestData['first_name'])) {
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
