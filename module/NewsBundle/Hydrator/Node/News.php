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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Hydrator\Node;

use NewsBundle\Entity\Node\News as NewsEntity;
use NewsBundle\Entity\Node\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts news data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class News extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new NewsEntity($this->getPersonEntity());
        }

        $endDate = self::loadDateTime($data['end_date']);

        if (null !== $endDate) {
            $object->setEndDate($endDate);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if (null !== $translation) {
                $translation->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ('' != $translationData['title'] && '' != $translationData['content']) {
                    $translation = new TranslationEntity(
                            $object,
                            $language,
                            $translationData['title'],
                            str_replace('#', '', $translationData['content'])
                        );
                    $object->addTranslation($translation);
                }
            }
        }

        $object->updateName();

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        if (null !== $object->getEndDate()) {
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        }

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        return $data;
    }
}
