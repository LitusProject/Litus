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

namespace FormBundle\Command;

use DateInterval;
use DateTime;
use FormBundle\Entity\Field\TimeSlot;
use FormBundle\Entity\Node\Form\Doodle;
use Zend\Mail\Message;

/**
 * Send reminder mails for forms.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Reminders extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('form:reminders')
            ->setDescription('Send reminder mails for forms')
            ->addOption('mail', 'm', null, 'Send the users a reminder')
            ->setHelp(
                <<<EOT
The %command.name% command sends users reminder mails for forms.
EOT
            );
    }

    protected function executeCommand()
    {
        $start = new DateTime();
        $start->setTime(0, 0);
        $start->add(new DateInterval('P1D'));

        $end = clone $start;
        $end->add(new DateInterval('P1D'));

        $timeSlots = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field\TimeSlot')
            ->findAllForReminderMail($start, $end);

        foreach ($timeSlots as $timeSlot) {
            $this->sendMailForTimeSlot($timeSlot);
        }
    }

    private function sendMailForTimeSlot(TimeSlot $timeSlot)
    {
        $form = $timeSlot->getForm();

        if (!($form instanceof Doodle)) {
            return;
        }

        $english = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $this->writeln('Form <comment>' . $form->getTitle($english) . '</comment>: TimeSlot <comment>' . $timeSlot->getLabel($english) . '</comment>');

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($timeSlot);

        $sendMails = $this->getOption('mail');
        if ($sendMails && getenv('APPLICATION_ENV') == 'development') {
            $sendMails = false;
            $this->writeln('<error>The mails will not be sent because the application is running in development mode!</error>');
        }

        foreach ($entries as $entry) {
            $this->writeln('Sending reminder for ' . $entry->getFormEntry()->getPersonInfo()->getFullName());

            if ($sendMails) {
                $form->setEntityManager($this->getEntityManager());
                $mailAddress = $form->getReminderMail()->getFrom();

                $language = $english;
                if ($entry->getFormEntry()->getCreationPerson()) {
                    $language = $entry->getFormEntry()->getCreationPerson()->getLanguage();
                }

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody($form->getCompletedReminderMailBody($entry->getFormEntry(), $language))
                    ->setFrom($mailAddress)
                    ->setSubject($form->getReminderMail()->getSubject())
                    ->addTo($entry->getFormEntry()->getPersonInfo()->getEmail(), $entry->getFormEntry()->getPersonInfo()->getFullName());

                if ($form->getReminderMail()->getBcc()) {
                    $mail->addBcc($mailAddress);
                }

                $this->getMailTransport()->send($mail);
            }
        }

        if ($sendMails) {
            $this->writeln('<comment>' . count($entries) . '</comment> mails have been sent');
        } else {
            $this->writeln('<comment>' . count($entries) . '</comment> mails would have been sent');
        }
    }
}
