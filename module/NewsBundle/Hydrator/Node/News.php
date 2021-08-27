<?php

namespace NewsBundle\Hydrator\Node;

use NewsBundle\Entity\Node\News as NewsEntity;
use NewsBundle\Entity\Node\News\Translation as TranslationEntity;

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
        if ($object === null) {
            $object = new NewsEntity($this->getPersonEntity());
        }

        $endDate = self::loadDateTime($data['end_date']);

        if ($endDate !== null) {
            $object->setEndDate($endDate);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ($translationData['title'] != '' && $translationData['content'] != '') {
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
        if ($object === null) {
            return array();
        }

        $data = array();

        if ($object->getEndDate() !== null) {
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        }

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        return $data;
    }
}
