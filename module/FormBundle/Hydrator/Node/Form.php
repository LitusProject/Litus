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

namespace FormBundle\Hydrator\Node;



use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    FormBundle\Entity\Node\Form\Doodle as DoodleEntity,
    FormBundle\Entity\Node\Translation\Form as TranslationEntity;

class Form extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('active', 'max', 'multiple', 'editable_by_user', 'send_guest_login_mail', 'non_member');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a form');
        }

        if (null !== $object->getId()) { // Check if this is a new form
            $group = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group\Mapping')
                ->findOneByForm($object);
        } else {
            $group = null;
        }

        if (null === $group) {
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

        if ($object instanceof DoodleEntity) {
            $object->setNamesVisibleForOthers($data['names_visible_for_others']);

            if ($data['reminder_mail']) {
                $object->setReminderMail(
                    $this->getHydrator('FormBundle\Hydrator\Mail\Mail')
                        ->hydrate($data['reminder_mail_form'], $object->getReminderMail())
                );
            } else {
                $object->setReminderMail(null);
            }
        }

        if ($data['mail']) {
            $object->setMail(
                $this->getHydrator('FormBundle\Hydrator\Mail\Mail')
                    ->hydrate($data['mail_form'], $object->getMail())
            );
        } else {
            $object->setMail(null);
        }

        foreach ($this->getLanguages() as $language) {
            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];
            if ('' != $translationData['title'] && '' != $translationData['introduction'] && '' != $translationData['submittext']) {
                $translation = $object->getTranslation($language, false);

                if (null === $translation) {
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
        if (null === $object) {
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

        $data = $this->stdExtract($object, self::$std_keys);

        $data['tab_content'] = array();
        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                'title' => $object->getTitle($language, false),
                'introduction' => $object->getIntroduction($language, false),
                'submittext' => $object->getSubmitText($language, false),
                'updatetext' => $object->getUpdateText($language, false),
            );
        }

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['mail'] = $object->hasMail();

        if ($object->hasMail()) {
            $data['mail_form'] = $this->getHydrator('FormBundle\Hydrator\Mail\Mail')
                ->extract($object->getMail());
        }

        if ($object instanceof DoodleEntity) {
            $data['names_visible_for_others'] = $object->getNamesVisibleForOthers();
            $data['reminder_mail'] = $object->hasReminderMail();

            if ($object->hasReminderMail()) {
                $data['reminder_mail_form'] = $this->getHydrator('FormBundle\Hydrator\Mail\Mail')
                    ->extract($object->getReminderMail());
            }
        }

        return $data;
    }
}
