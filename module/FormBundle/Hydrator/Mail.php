<?php

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
