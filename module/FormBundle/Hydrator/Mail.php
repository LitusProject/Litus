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

namespace FormBundle\Hydrator;

use FormBundle\Entity\Mail as MailEntity;
use FormBundle\Entity\Mail\Translation as TranslationEntity;

class Mail extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('from', 'bcc');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new MailEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $this->getEntityManager()->persist($object);

        foreach ($this->getLanguages() as $language) {
            $mailData = $data['tab_content']['tab_' . $language->getAbbrev()];
            $translation = $object->getTranslation($language, false);

            if ($translation === null) {
                $translation = new TranslationEntity(
                    $object,
                    $language,
                    $mailData['subject'],
                    $mailData['body']
                );
                $this->getEntityManager()->persist($translation);
            } else {
                $translation->setSubject($mailData['subject'])
                    ->setContent($mailData['body']);
            }
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['subject'] = $object->getSubject($language, false);

            if ($object->getContent($language, false) != '') {
                $data['tab_content']['tab_' . $language->getAbbrev()]['body'] = $object->getContent($language, false);
            }
        }

        return $data;
    }
}
