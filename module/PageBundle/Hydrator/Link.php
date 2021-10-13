<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Link as LinkEntity;
use PageBundle\Entity\Link\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts page data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Link extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new LinkEntity();
        }

        $category = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findOneById($data['category']);

        $forcedLanguage = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev($data['forced_language']);

        $object->setCategory($category)
            ->setForcedLanguage($forcedLanguage)
            ->setOrderNumber($data['order_number'])
            ->setActive($data['active']);



        if ($data['parent_' . $category->getId()] != '') {
            $parent = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneById($data['parent_' . $category->getId()]);

            if ($parent !== null) {
                $object->setParent($parent);
            }
        } else {
            $object->setParent();
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setName($translationData['name'])
                    ->setUrl($translationData['url']);
            } else {
                if ($translationData['name'] != '' && $translationData['url'] != '') {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $translationData['name'],
                        $translationData['url']
                    );

                    $this->getEntityManager()->persist($translation);
                }
            }
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['name'] = $object->getName($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['url'] = $object->getUrl($language, false);
        }
        $data['forced_language'] = $object->getForcedLanguage() ? $object->getForcedLanguage()->getAbbrev() : '';
        $data['order_number'] = $object->getOrderNumber();
        $data['active'] = $object->isActive();

        $data['category'] = $object->getCategory()->getId();
        $data['parent_' . $object->getCategory()->getId()] = $object->getParent() ? $object->getParent()->getId() : '';

        return $data;
    }
}
