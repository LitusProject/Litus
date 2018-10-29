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

namespace NotificationBundle\Hydrator\Node;

use CommonBundle\Component\Hydrator\Exception\InvalidDateException;
use NotificationBundle\Entity\Node\Notification as NotificationEntity;
use NotificationBundle\Entity\Node\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts notification data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Notification extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('active');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new NotificationEntity($this->getPersonEntity());
        }

        $startDate = self::loadDateTime($data['start_date']);
        $endDate = self::loadDateTime($data['end_date']);

        if ($startDate === null || $endDate === null) {
            throw new InvalidDateException();
        }

        $object->setEndDate($endDate)
            ->setStartDate($startDate)
            ->setActive($data['active']);

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setContent($translationData['content']);
            } else {
                if ($translationData['content'] != '') {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        str_replace('#', '', $translationData['content'])
                    );
                    $object->addTranslation($translation);
                }
            }
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        if ($object->getEndDate() !== null) {
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        }

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['content'] = $object->getContent($language, false);
        }

        return $data;
    }
}
