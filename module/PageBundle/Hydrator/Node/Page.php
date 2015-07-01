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

namespace PageBundle\Hydrator\Node;

use Locale,
    PageBundle\Entity\Node\Page as PageEntity,
    PageBundle\Entity\Node\Translation as TranslationEntity;

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

        if (null !== $object && null !== $object->getName()) {
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

        $newPage->setCategory($category)
            ->setEditRoles($editRoles)
            ->setName($data['tab_content']['tab_' . $fallbackLanguage]['title']);

        if ('' != $data['parent_' . $category->getId()]) {
            $parent = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneById($data['parent_' . $category->getId()]);

            $newPage->setParent($parent);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $newPage->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if (null !== $translation) {
                $translation->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ('' != $translationData['title'] && '' != $translationData['content']) {
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
        if (null === $object) {
            return array();
        }

        $data = array();

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        $data['category'] = $object->getCategory()->getId();

        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        $data['parent_' . $object->getCategory()->getId()] = null !== $object->getParent() ? $object->getParent()->getId() : '';

        return $data;
    }
}
