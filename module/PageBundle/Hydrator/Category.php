<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Category as CategoryEntity;
use PageBundle\Entity\Category\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts page data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Category extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CategoryEntity();
        }

        if ($data['parent'] != '') {
            $parent = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneById($data['parent']);

            $object->setParent($parent);
        } else {
            $object->setParent(null);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            //No spaces in name to create valide url for categoryPage
            $translationData['name'] = str_replace(' ', '-', $translationData['name']);

            if ($translation !== null) {
                $translation->setName($translationData['name']);
            } else {
                if ($translationData['name'] != '') {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $translationData['name']
                    );

                    // this persists the translations even if the returned
                    // object is never persisted.
                    // This should never happen though.
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
        }

        $data['parent'] = $object->getParent() ? $object->getParent()->getId() : '';

        return $data;
    }
}
