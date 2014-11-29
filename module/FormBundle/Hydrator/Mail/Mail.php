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

namespace FormBundle\Hydrator\Mail;

use FormBundle\Entity\Mail\Mail as MailEntity,
    FormBundle\Entity\Mail\Translation as TranslationEntity;

class Mail extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('from', 'bcc');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new MailEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$std_keys);
        $this->getEntityManager()->persist($object);

        foreach ($this->getLanguages() as $language) {
            $mailData = $data['tab_content']['tab_' . $language->getAbbrev()];
            $translation = $object->getTranslation($language, false);

            if (null === $translation) {
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
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['subject'] = $object->getSubject($language, false);

            if ($object->getContent($language, false) != '') {
                $data['tab_content']['tab_' . $language->getAbbrev()]['body'] = $object->getContent($language, false);
            }
        }

        return $data;
    }
}
