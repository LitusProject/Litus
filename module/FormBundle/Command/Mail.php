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

namespace FormBundle\Command;

use DateTime,
    DateInterval,
    FormBundle\Entity\Field\TimeSlot,
    FormBundle\Entity\Node\Form\Form,
    Zend\Mail\Message;

/**
 * RenderMail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Mail extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('form:mail')
            ->setDescription('renders (and sends) reminder mails for forms')
            ->addOption('mail', 'm', null, 'send the reminder mails')
            ->setHelp(<<<EOT
The %command.name% command generates reminder mails for forms and sends them
if the <fg=blue>--mail</fg=blue> flag is given.

The <fg=blue>--mail</fg=blue> flag is ignored if APPLICATON_ENV is "<comment>development</comment>"
EOT
        );
    }

    protected function executeCommand()
    {
        if ($this->getOption('mail') && 'development' == getenv('APPLICATION_ENV')) {
            $this->writeln('<fg=red;options=bold>Warning:</fg=red;options=bold> APPLICATION_ENV is development, --mail is ignored');
        }

        $start = new DateTime();
        $start->setTime(0, 0);
        $start->add(new DateInterval('P1D'));

        $end = clone $start;
        $end->add(new DateInterval('P1D'));

        $timeSlots = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field\TimeSlot')
            ->findAllForReminderMail($start, $end);

        foreach ($timeSlots as $timeSlot) {
            $this->_sendMailForTimeSlot($timeSlot);
        }
    }

    protected function getLogName()
    {
        return 'FormMail';
    }

    private function _sendMailForTimeSlot(TimeSlot $timeSlot)
    {
        $form = $timeSlot->getForm();

        if ($form instanceof Form)
            return;

        $english = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $this->writeln('Form <comment>' . $form->getTitle($english)
            . '</comment>: TimeSlot <comment>' . $timeSlot->getLabel($english) . '</comment>');

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($timeSlot);

        foreach ($entries as $entry) {
            $this->writeln('Reminder for ' . $entry->getFormEntry()->getPersonInfo()->getFullName());

            $form->setEntityManager($this->getEntityManager());
            $mailAddress = $form->getReminderMail()->getFrom();

            $language = $english;
            if ($entry->getFormEntry()->getCreationPerson())
                $language = $entry->getFormEntry()->getCreationPerson()->getLanguage();

            $mail = new Message();
            $mail->setBody($form->getCompletedReminderMailBody($entry->getFormEntry(), $language))
                ->setFrom($mailAddress)
                ->setSubject($form->getReminderMail()->getSubject())
                ->addTo($entry->getFormEntry()->getPersonInfo()->getEmail(), $entry->getFormEntry()->getPersonInfo()->getFullName());

            if ($form->getReminderMail()->getBcc())
                $mail->addBcc($mailAddress);

            if ('development' != getenv('APPLICATION_ENV') && $this->getOption('mail'))
                $this->getMailTransport()->send($mail);
        }
    }
}
