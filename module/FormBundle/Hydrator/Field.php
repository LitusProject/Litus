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

namespace FormBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    FormBundle\Entity\Field\Checkbox as CheckboxFieldEntity,
    FormBundle\Entity\Field\Dropdown as DropdownFieldEntity,
    FormBundle\Entity\Field\File as FileFieldEntity,
    FormBundle\Entity\Field\String as StringFieldEntity,
    FormBundle\Entity\Field\TimeSlot as TimeSlotFieldEntity,
    FormBundle\Entity\Field\Translation\Option as OptionTranslationFieldEntity,
    FormBundle\Entity\Field\Translation\TimeSlot as TimeSlotTranslationFieldEntity,
    FormBundle\Entity\Translation as TranslationEntity;

class Field extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('order', 'required');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a field');
        }

        if ($object instanceof TimeSlotFieldEntity) {
            $data['order'] = 0;
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $visibleId = $data['visibility']['if'] == 'always' ? 0 : $data['visibility']['if'];

        $visibilityDecissionField = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findOneById($visibleId);

        $object->setVisibilityDecissionField($visibilityDecissionField)
            ->setVisibilityValue(isset($visibilityDecissionField) ? $data['visibility']['value'] : null);

        if ($object instanceof StringFieldEntity) {
            $stringData = $data['string_form'];
            $object->setLineLength($stringData['charsperline'] === '' ? 0 : $stringData['charsperline'])
                ->setLines($stringData['lines'] === '' ? 0 : $stringData['lines'])
                ->setMultiLine($stringData['multiline']);
        } elseif ($object instanceof DropdownFieldEntity) {
            $dropdownData = $data['dropdown_form'];
            foreach ($this->getLanguages() as $language) {
                $languageData = $dropdownData['tab_content']['tab_' . $language->getAbbrev()];
                if ('' != $languageData['options']) {
                    $translation = $object->getOptionTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setOptions($languageData['options']);
                    } else {
                        $translation = new OptionTranslationFieldEntity(
                            $object,
                            $language,
                            $languageData['options']
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }
            }
        } elseif ($object instanceof FileFieldEntity) {
            $fileData = $data['file_form'];
            print_r($fileData);
            //exit();
            $object->setMaxSize($fileData['max_size'] === '' ? 25 : intval($fileData['max_size']));
        } elseif ($object instanceof TimeslotFieldEntity) {
            $timeslotData = $data['timeslot_form'];
            $startDate = self::loadDateTime($timeslotData['start_date']);
            $endDate = self::loadDateTime($timeslotData['end_date']);

            if (!$startDate || !$endDate) {
                throw new \RuntimeException('Timeslot has invalid dates.');
            }

            $object->setStartDate($startDate)
                ->setEndDate($endDate);

            foreach ($this->getLanguages() as $language) {
                $languageData = $timeslotData['tab_content']['tab_' . $language->getAbbrev()];
                $translation = $object->getTimeSlotTranslation($language, false);

                if ('' == $languageData['location'] && '' == $languageData['extra_info']) {
                    if (null !== $translation) {
                        $this->getEntityManager()->remove($translation);
                    }
                    continue;
                }

                if (null !== $translation) {
                    $translation->setLocation($languageData['location'])
                        ->setExtraInformation($languageData['extra_info']);
                } else {
                    $translation = new TimeSlotTranslationFieldEntity(
                        $object,
                        $language,
                        $languageData['location'],
                        $languageData['extra_info']
                    );

                    $this->getEntityManager()->persist($translation);
                }
            }
        }

        foreach ($this->getLanguages() as $language) {
            $languageData = $data['tab_content']['tab_' . $language->getAbbrev()];
            if ('' != $languageData['label']) {
                $translation = $object->getTranslation($language, false);

                if (null === $translation) {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $languageData['label']
                    );
                    $this->getEntityManager()->persist($translation);
                } else {
                    $translation->setLabel($languageData['label']);
                }
            } else {
                $translation = $object->getTranslation($language, false);

                if ($translation !== null) {
                    $this->getEntityManager()->remove($translation);
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

        $data = $this->stdExtract($object, self::$stdKeys);

        if ($object->getVisibilityDecissionField()) {
            $data['visibility']['if'] = $object->getVisibilityDecissionField()->getId();
            $data['visibility']['value'] = $object->getVisibilityValue();
        } else {
            $data['visibility']['if'] = 'always';
        }

        if ($object instanceof StringFieldEntity) {
            $data['type'] = 'string';

            $lines = 0;
            if ($object->isMultiLine()) {
                $lines = $object->getLines();
            }

            $data['string_form'] = array(
                'charsperline' => $object->getLineLength(),
                'multiline' => $object->isMultiLine(),
                'lines' => $lines,
            );
        } elseif ($object instanceof DropdownFieldEntity) {
            $data['type'] = 'dropdown';

            foreach ($this->getLanguages() as $language) {
                $translation = $object->getOptionTranslation($language, false);
                if (null !== $translation) {
                    $data['dropdown_form']['tab_content']['tab_' . $language->getAbbrev()]['options'] = $translation->getOptions();
                }
            }
        } elseif ($object instanceof CheckboxFieldEntity) {
            $data['type'] = 'checkbox';
        } elseif ($object instanceof FileFieldEntity) {
            $data['type'] = 'file';
            $data['file_form']['max_size'] = $object->getMaxSize();
        } elseif ($object instanceof TimeslotFieldEntity) {
            $data['type'] = 'timeslot';
            $data['timeslot_form'] = array(
                'start_date' => $object->getStartDate()->format('d/m/Y H:i'),
                'end_date' => $object->getEndDate()->format('d/m/Y H:i'),
            );

            foreach ($this->getLanguages() as $language) {
                $translation = $object->getTimeSlotTranslation($language, false);
                if (null !== $translation) {
                    $data['timeslot_form']['tab_content']['tab_' . $language->getAbbrev()] = array(
                        'location' => $translation->getLocation(),
                        'extra_info' => $translation->getExtraInformation(),
                    );
                }
            }
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);
            if (null !== $translation) {
                $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                    'label' => $translation->getLabel(),
                );
            }
        }

        return $data;
    }
}
