<?php

namespace FormBundle\Hydrator\Node;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use FormBundle\Entity\Node\Form\GroupMap as GroupMapEntity;
use FormBundle\Entity\Node\Group\Translation as TranslationEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('active', 'max', 'editable_by_user', 'non_member');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a form');
        }

        foreach ($this->getLanguages() as $language) {
            $languageData = $data['tab_content']['tab_' . $language->getAbbrev()];
            $translation = $object->getTranslation($language, false);

            if ($languageData['title'] != '' && $languageData['introduction'] != '') {
                if ($translation === null) {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $languageData['title'],
                        $languageData['introduction']
                    );
                } else {
                    $translation->setTitle($languageData['title'])
                        ->setIntroduction($languageData['introduction']);
                }

                $this->getEntityManager()->persist($translation);
            } else {
                if ($translation !== null) {
                    $this->getEntityManager()->remove($translation);
                }
            }
        }

        if (isset($data['start_form'])) {
            $startForm = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findOneById($data['start_form']);

            $this->getEntityManager()->persist(new GroupMapEntity($startForm, $object, 1));
        } else {
            $object->setStartDate(self::loadDateTime($data['start_date']))
                ->setEndDate(self::loadDateTime($data['end_date']))
                ->setActive($data['active'])
                ->setMax($data['max'] == '' ? 0 : $data['max'])
                ->setEditableByUser($data['editable_by_user'])
                ->setNonMember($data['non_member']);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            if ($translation !== null) {
                $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                    'title'        => $translation->getTitle(),
                    'introduction' => $translation->getIntroduction(),
                );
            }
        }

        return $data;
    }
}
