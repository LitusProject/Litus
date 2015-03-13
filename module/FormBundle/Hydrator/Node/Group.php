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
    FormBundle\Entity\Node\Group\Mapping as MappingEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('active', 'max', 'editable_by_user', 'non_member');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a form');
        }

        foreach ($this->getLanguages() as $language) {
            $languageData = $data['tab_content']['tab_' . $language->getAbbrev()];
            $translation = $object->getTranslation($language, false);

            if ('' != $languageData['title'] && '' != $languageData['introduction']) {
                if (null === $translation) {
                    $translation = new GroupTranslation(
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

            $this->getEntityManager()->persist(new MappingEntity($startForm, $object, 1));
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
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            if (null !== $translation) {
                $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                    'title' => $translation->getTitle(),
                    'introduction' => $translation->getIntroduction(),
                );
            }
        }

        return $data;
    }
}
