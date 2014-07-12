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

namespace CalendarBundle\Hydrator\Node;

use CalendarBundle\Entity\Node\Translation as TranslationEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidDateException,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    DateTime;

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
        // EventEntity requires the Person that created it, so
        // we cannot create an object here.
        if (null === $object)
            throw new InvalidObjectException();

        $startDate = self::_loadDate($data['start_date']);

        if (null === $startDate)
            throw new InvalidDateException();

        $object->setStartDate($startDate)
            ->setEndDate(self::_loadDate($data['end_date']));

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if (null !== $translation) {
                $translation->setLocation($translationData['location'])
                    ->setTitle($translationData['title'])
                    ->setContent($translationData['content']);
            } else {
                if ('' != $translationData['location'] && '' != $translationData['title'] && '' != $translationData['content']) {
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
        if (null === $object) {
            return array();
        }

        $data = array();

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        if (null !== $object->getEndDate())
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['title'] = $object->getTitle($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['location'] = $object->getLocation($language, false);
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        return $data;
    }

    private function getLanguages()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
