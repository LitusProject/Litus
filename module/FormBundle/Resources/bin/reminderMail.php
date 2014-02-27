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

/**
 * Send reminder mail for doodle
 *
 * Usage:
 * --run|-r     Run
 * --mail|-m    Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');
$mt = $application->getServiceManager()->get('mail_transport');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

$rules = array(
    'run|r-s' => 'Run',
    'mail|m'  => 'Send Mail',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $english = $em->getRepository('CommonBundle\Entity\General\Language')
        ->findOneByAbbrev('en');

    $start = new \DateTime();
    $start->setTime(0, 0);
    $start->add(new \DateInterval('P1D'));

    $end = clone $start;
    $end->add(new \DateInterval('P1D'));

    try {
    $timeSlots = $em->getRepository('FormBundle\Entity\Field\TimeSlot')
        ->findAllForReminderMail($start, $end);
    } catch(\Exception $e) {
        print_r($e);
    }

    foreach($timeSlots as $timeSlot) {
        echo 'Form ' . $timeSlot->getForm()->getTitle($english) . ': TimeSlot ' . $timeSlot->getLabel($english) . PHP_EOL;
        $entries = $em->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($timeSlot);

        foreach($entries as $entry) {
            echo 'Reminder for ' . $entry->getFormEntry()->getPersonInfo()->getFullName() . PHP_EOL;

            $timeSlot->getForm()->setEntityManager($em);
            $mailAddress = $timeSlot->getForm()->getReminderMail()->getFrom();

            $language = $english;
            if ($entry->getFormEntry()->getCreationPerson())
                $language = $entry->getFormEntry()->getCreationPerson()->getLanguage();

            $mail = new \Zend\Mail\Message();
            $mail->setBody($timeSlot->getForm()->getCompletedReminderMailBody($entry->getFormEntry(), $language))
                ->setFrom($mailAddress)
                ->setSubject($timeSlot->getForm()->getReminderMail()->getSubject())
                ->addTo($entry->getFormEntry()->getPersonInfo()->getEmail(), $entry->getFormEntry()->getPersonInfo()->getFullName());

            if ($timeSlot->getForm()->getReminderMail()->getBcc())
                $mail->addBcc($mailAddress);

            if ('development' != getenv('APPLICATION_ENV') && isset($opts->m))
                $mt->send($mail);
        }

        echo '------------------------------------------------' . PHP_EOL;
    }
}