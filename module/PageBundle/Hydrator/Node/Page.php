<?php

namespace PageBundle\Hydrator\Node;

use Locale;
use PageBundle\Entity\Node\Page as PageEntity;
use PageBundle\Entity\Node\Page\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts page data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Page extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        $newPage = new PageEntity($this->getPersonEntity());

        if ($object !== null && $object->getName() !== null) {
            $object->close();
            $newPage->setName($object->getName());

            $this->getEntityManager()->persist($newPage);

            $orphanedPages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findByParent($object->getId());

            foreach ($orphanedPages as $orphanedPage) {
                $orphanedPage->setParent($newPage);
            }

            $orphanedCategories = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Category')
                ->findByParent($object->getId());

            foreach ($orphanedCategories as $orphanedCategory) {
                $orphanedCategory->setParent($newPage);
            }

            $orphanedLinks = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findByParent($object->getId());

            foreach ($orphanedLinks as $orphanedLink) {
                $orphanedLink->setParent($newPage);
            }
        }

        $category = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findOneById($data['category']);

        $editRoles = array();
        if (isset($data['edit_roles'])) {
            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($editRole);
            }
        }

        $fallbackLanguage = Locale::getDefault();

        $forcedLanguage = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev($data['forced_language']);

        $newPage->setCategory($category)
            ->setEditRoles($editRoles)
            ->setName($data['tab_content']['tab_' . $fallbackLanguage]['title'])
            ->setForcedLanguage($forcedLanguage)
            ->setOrderNumber($data['order_number'])
            ->setActive($data['active']);

        if ($data['parent_' . $category->getId()] != '') {
            $parent = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneById($data['parent_' . $category->getId()]);

            $newPage->setParent($parent);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $newPage->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ($translationData['title'] != '' && $translationData['content'] != '') {
                    $translation = new TranslationEntity(
                        $newPage,
                        $language,
                        $translationData['title'],
                        $translationData['content']
                    );

                    $this->getEntityManager()->persist($translation);
                }
            }
        }

        return $newPage;
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

        $data['category'] = $object->getCategory()->getId();
        $data['forced_language'] = $object->getForcedLanguage() ? $object->getForcedLanguage()->getAbbrev() : '';
        $data['order_number'] = $object->getOrderNumber();
        $data['active'] = $object->isActive();


        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        $data['parent_' . $object->getCategory()->getId()] = $object->getParent() !== null ? $object->getParent()->getId() : '';

        return $data;
    }
}
