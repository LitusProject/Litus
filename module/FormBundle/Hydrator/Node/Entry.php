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

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    FormBundle\Entity\Entry as FieldEntryEntity,
    FormBundle\Entity\Field as FieldEntity,
    FormBundle\Entity\Field\File as FileFieldEntity,
    FormBundle\Entity\Node\Entry as FormEntryEntity;

class Entry extends \CommonBundle\Component\Hydrator\Hydrator
{
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

        $object->setDraft(isset($data['save_as_draft']) && $data['save_as_draft']);

        foreach ($object->getForm()->getFields() as $field) {
            $value = isset($fieldData['field-' . $field->getId()]) ? $fieldData['field-' . $field->getId()] : '';

            if ($object->getId()) {
                $fieldEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Entry')
                    ->findOneByFormEntryAndField($object, $field);
            } else {
                $fieldEntry = null;
            }
            $removed = false;
            $readableValue = null;

            if ($field instanceof FileFieldEntity) {
                list($removed, $value, $readableValue) = $this->processFileField($field, $object, $fieldData, $fieldEntry);
            }

            if (!$removed) {
                if (null !== $fieldEntry) {
                    $fieldEntry->setValue($value)
                        ->setReadableValue($readableValue);
                } else {
                    $fieldEntry = new FieldEntryEntity($object, $field, $value, $readableValue);
                    $object->addFieldEntry($fieldEntry);
                }
            }
        }

        return $object;
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

    /**
     * @param  FieldEntity           $field
     * @param  FormEntryEntity       $formEntry
     * @param  array                 $data
     * @param  FieldEntryEntity|null $fieldEntry
     * @return array
     */
    private function processFileField(FieldEntity $field, FormEntryEntity $formEntry, $data, FieldEntryEntity $fieldEntry = null)
    {
        $removed = false;
        $value = '';
        $readableValue = '';
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path');

        if (isset($data['field-' . $field->getId() . '-removed'])) {
            $removed = true;

            if (isset($fieldEntry)) {
                if (file_exists($filePath . '/' . $fieldEntry->getValue())) {
                    unlink($filePath . '/' . $fieldEntry->getValue());
                }

                $formEntry->removeFieldEntry($fieldEntry);
            }
        } elseif (is_array($data['field-' . $field->getId()]) && $data['field-' . $field->getId()]['size'] > 0) {
            if (null === $fieldEntry || $fieldEntry->getValue() == '') {
                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . '/' . $fileName));
            } else {
                $fileName = $fieldEntry->getValue();
                if (file_exists($filePath . '/' . $fileName)) {
                    unlink($filePath . '/' . $fileName);
                }
            }

            move_uploaded_file($data['field-' . $field->getId()]['tmp_name'], $filePath . '/' . $fileName);

            $readableValue = basename($data['field-' . $field->getId()]['name']);
            $value = $fileName;

            if ($value == '' && null !== $fieldEntry) {
                $value = $fieldEntry->getValue();
            }
        } elseif (null !== $fieldEntry) {
            $value = $fieldEntry->getValue();
            $readableValue = $fieldEntry->getReadableValue();
        }

        return array($removed, $value, $readableValue);
    }
}
