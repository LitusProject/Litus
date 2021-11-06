<?php

namespace CommonBundle\Hydrator\General\Node\FAQ;

use CommonBundle\Entity\General\Node\FAQ\FAQ as FAQEntity;
use CommonBundle\Entity\General\Node\FAQ\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts faq data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FAQ extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        $newFAQ = new FAQEntity($this->getPersonEntity());

        if ($object !== null && $object->getName() !== null) {
            $newFAQ->setName($object->getName());
            $this->getEntityManager()->remove($object);
            $this->getEntityManager()->persist($newFAQ);
        }

        $editRoles = array();
        if (isset($data['edit_roles'])) {
            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($editRole);
            }
        }

        $forcedLanguage = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev($data['forced_language']);

        $newFAQ->setEditRoles($editRoles)
            ->setName($data['name'])
            ->setForcedLanguage($forcedLanguage)
            ->setOrderNumber($data['order_number'])
            ->setActive($data['active']);

        foreach ($this->getLanguages() as $language) {
            $translation = $newFAQ->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ($translationData['title'] != '' && $translationData['content'] != '') {
                    $translation = new TranslationEntity(
                        $newFAQ,
                        $language,
                        $translationData['title'],
                        $translationData['content']
                    );

                    $this->getEntityManager()->persist($translation);
                }
            }
        }

        return $newFAQ;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        $data['forced_language'] = $object->getForcedLanguage() ? $object->getForcedLanguage()->getAbbrev() : '';
        $data['order_number'] = $object->getOrderNumber();
        $data['active'] = $object->isActive();
        $data['name'] = $object->getName();


        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        return $data;
    }
}
