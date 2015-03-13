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

namespace PageBundle\Hydrator;

use PageBundle\Entity\Category as CategoryEntity,
    PageBundle\Entity\Category\Translation as TranslationEntity;

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
        if (null === $object) {
            $object = new CategoryEntity();
        }

        if ('' != $data['parent']) {
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

            if (null !== $translation) {
                $translation->setName($translationData['name']);
            } else {
                if ('' != $translationData['name']) {
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
        if (null === $object) {
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
