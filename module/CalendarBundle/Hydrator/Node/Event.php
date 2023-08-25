<?php

namespace CalendarBundle\Hydrator\Node;

use CalendarBundle\Entity\Node\Event as EventEntity;
use CalendarBundle\Entity\Node\Event\Translation as TranslationEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidDateException;

/**
 * This hydrator hydrates/extracts event data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new EventEntity($this->getPersonEntity());
        }

        $startDate = self::loadDateTime($data['start_date']);

        if ($startDate === null) {
            throw new InvalidDateException();
        }

        $object->setStartDate($startDate)
            ->setEndDate(self::loadDateTime($data['end_date']));
        $object->setIsHidden($data['is_hidden']);
        $object->setIsCareer($data['is_career']);

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setLocation($translationData['location'])
                    ->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ($translationData['location'] != '' && $translationData['title'] != '' && $translationData['content'] != '') {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $translationData['location'],
                        $translationData['title'],
                        $translationData['content']
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

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        if ($object->getEndDate() !== null) {
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        }
        $data['is_hidden'] = $object->isHidden();

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['location'] = $object->getLocation($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        return $data;
    }
}
