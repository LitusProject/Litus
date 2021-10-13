<?php

namespace FormBundle\Hydrator\Node;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use FormBundle\Entity\Node\Form\Doodle as DoodleEntity;
use FormBundle\Entity\Node\Form\Translation as TranslationEntity;

class Form extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('active', 'max', 'multiple', 'editable_by_user', 'send_guest_login_mail', 'non_member');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a form');
        }

        if ($object->getId() !== null) {
            // Check if this is a new form
            $group = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
                ->findOneByForm($object);
        } else {
            $group = null;
        }

        if ($group === null) {
            $object->setStartDate(self::loadDateTime($data['start_date']))
                ->setEndDate(self::loadDateTime($data['end_date']))
                ->setActive($data['active'])
                ->setEditableByUser($data['editable_by_user'])
                ->setNonMember($data['non_member'])
                ->setSendGuestLoginMail($data['send_guest_login_mail']);

            if ($object instanceof DoodleEntity || $data['max'] == '') {
                $object->setMax(0);
            } else {
                $object->setMax($data['max']);
            }
        }

        $object->setMultiple($data['multiple']);

        $hydrator = $this->getHydrator('FormBundle\Hydrator\Mail');

        if ($object instanceof DoodleEntity) {
            $object->setNamesVisibleForOthers($data['names_visible_for_others']);

            if ($data['reminder_mail']) {
                $object->setReminderMail(
                    $hydrator->hydrate($data['reminder_mail_form'], $object->getReminderMail())
                );
            } else {
                $object->setReminderMail(null);
            }
        }

        if ($data['mail']) {
            $object->setMail(
                $hydrator->hydrate($data['mail_form'], $object->getMail())
            );
        } else {
            $object->setMail(null);
        }

        foreach ($this->getLanguages() as $language) {
            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];
            if ($translationData['title'] != '' && $translationData['introduction'] != '' && $translationData['submittext'] != '') {
                $translation = $object->getTranslation($language, false);

                if ($translation === null) {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $translationData['title'],
                        $translationData['introduction'],
                        $translationData['submittext'],
                        $translationData['updatetext']
                    );
                    $this->getEntityManager()->persist($translation);
                } else {
                    $translation->setTitle($translationData['title'])
                        ->setIntroduction($translationData['introduction'])
                        ->setSubmitText($translationData['submittext'])
                        ->setUpdateText($translationData['updatetext']);
                }
            } else {
                $translation = $object->getTranslation($language, false);

                if ($translation !== null) {
                    $this->getEntityManager()->remove($translation);
                }
            }
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            $mailContent = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('form.mail_confirmation')
            );

            $reminderMailContent = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('form.mail_reminder')
            );

            $data = array();

            foreach ($this->getLanguages() as $language) {
                if (isset($mailContent[$language->getAbbrev()])) {
                    $data['mail_form']['content']['tab_content']['tab_' . $language->getAbbrev()]['body'] = $mailContent[$language->getAbbrev()]['content'];
                }

                if (isset($reminderMailContent[$language->getAbbrev()])) {
                    $data['reminder_mail_form']['content']['tab_content']['tab_' . $language->getAbbrev()]['body'] = $reminderMailContent[$language->getAbbrev()]['content'];
                }
            }

            return $data;
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['tab_content'] = array();
        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                'title'        => $object->getTitle($language, false),
                'introduction' => $object->getIntroduction($language, false),
                'submittext'   => $object->getSubmitText($language, false),
                'updatetext'   => $object->getUpdateText($language, false),
            );
        }

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['mail'] = $object->hasMail();

        $hydrator = $this->getHydrator('FormBundle\Hydrator\Mail');

        if ($object->hasMail()) {
            $data['mail_form'] = $hydrator->extract($object->getMail());
        }

        if ($object instanceof DoodleEntity) {
            $data['names_visible_for_others'] = $object->getNamesVisibleForOthers();
            $data['reminder_mail'] = $object->hasReminderMail();

            if ($object->hasReminderMail()) {
                $data['reminder_mail_form'] = $hydrator->extract($object->getReminderMail());
            }
        }

        return $data;
    }
}
