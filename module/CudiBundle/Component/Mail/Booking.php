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

namespace CudiBundle\Component\Mail;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\EntityManager;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;

/**
 * Booking
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Booking
{
    /**
     * Send a mail for assigned bookings
     *
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param array              $bookings
     * @param Person             $person
     */
    public static function sendAssignMail(EntityManager $entityManager, TransportInterface $mailTransport, $bookings, Person $person)
    {
        $language = $person->getLanguage();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.booking_assigned_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $openingHours = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findPeriodFromNow('P7D');

        $openingHourText = '';
        foreach ($openingHours as $openingHour) {
            $openingHourText .= '- ' . $openingHour->getStart()->format('l') . ' (' . $openingHour->getStart()->format('d/m') . ') : ' . $openingHour->getStart()->format('G:i') . ' - ' . $openingHour->getEnd()->format('G:i');

            if (strlen($openingHour->getComment($language)) > 0) {
                $openingHourText .= ' (' . $openingHour->getComment($language) . ')';
            }

            $openingHourText .= "\r\n";
        }

        if ($openingHourText == '') {
            $message = str_replace('#no_opening_hours#', '', $message);
        } else {
            $message = preg_replace('/#no_opening_hours#.*#no_opening_hours#/', '', $message);
        }

        preg_match('/#expires#(.*)#expires#/', $message, $matches);
        $message = preg_replace('/#expires#.*#expires#/', '', $message);

        $list = '';
        foreach ($bookings as $booking) {
            $list .= '* ' . $booking->getArticle()->getMainArticle()->getTitle() . ' ' . ($booking->getExpirationDate() ? '(' . $matches[1] . ' ' . $booking->getExpirationDate()->format('d/m/Y') : '') . ")\r\n";
        }

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ bookings }}', $list, str_replace('{{ openingHours }}', $openingHourText, $message)))
            ->setFrom($mailAddress, $mailName)
            ->addTo($person->getEmail(), $person->getFullName())
            ->setSubject($subject);

        $sendMailsToSysadmin = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_sysadmin') == 1;
        if ($sendMailsToSysadmin) {
            $mail->addBcc(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            );
        }

        $sendMailsToCudi = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_cudi') == 1;
        if ($sendMailsToCudi) {
            $mail->addCc($mailAddress, $mailName);
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $mailTransport->send($mail);
        }
    }

    /**
     * Send a warning mail before expiring bookings
     *
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param array              $bookings
     * @param Person             $person
     */
    public static function sendExpireWarningMail(EntityManager $entityManager, TransportInterface $mailTransport, $bookings, Person $person)
    {
        $language = $person->getLanguage();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.booking_expire_warning_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $openingHours = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findPeriodFromNow('P7D');

        $openingHourText = '';
        foreach ($openingHours as $openingHour) {
            $openingHourText .= '- ' . $openingHour->getStart()->format('l') . ' (' . $openingHour->getStart()->format('d/m') . ') : ' . $openingHour->getStart()->format('G:i') . ' - ' . $openingHour->getEnd()->format('G:i');

            if (strlen($openingHour->getComment($language)) > 0) {
                $openingHourText .= ' (' . $openingHour->getComment($language) . ')';
            }

            $openingHourText .= "\r\n";
        }

        if ($openingHourText == '') {
            $message = str_replace('#no_opening_hours#', '', $message);
        } else {
            $message = preg_replace('/#no_opening_hours#.*#no_opening_hours#/', '', $message);
        }

        preg_match('/#expires#(.*)#expires#/', $message, $matches);
        $message = preg_replace('/#expires#.*#expires#/', '', $message);

        $list = '';
        foreach ($bookings as $booking) {
            $list .= '* ' . $booking->getArticle()->getMainArticle()->getTitle() . ' ' . ($booking->getExpirationDate() ? '(' . $matches[1] . ' ' . $booking->getExpirationDate()->format('d/m/Y') : '') . ")\r\n";
        }

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ bookings }}', $list, str_replace('{{ openingHours }}', $openingHourText, $message)))
            ->setFrom($mailAddress, $mailName)
            ->addTo($person->getEmail(), $person->getFullName())
            ->setSubject($subject);

        $sendMailsToSysadmin = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_sysadmin') == 1;
        if ($sendMailsToSysadmin) {
            $mail->addBcc(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            );
        }

        $sendMailsToCudi = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_cudi') == 1;
        if ($sendMailsToCudi) {
            $mail->addCc($mailAddress, $mailName);
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $mailTransport->send($mail);
        }
    }

    /**
     * Send a mail when expiring bookings
     *
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param array              $bookings
     * @param Person             $person
     */
    public static function sendExpireMail(EntityManager $entityManager, TransportInterface $mailTransport, $bookings, Person $person)
    {
        $language = $person->getLanguage();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.booking_expire_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $openingHours = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findPeriodFromNow('P7D');

        $language = $entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $openingHourText = '';
        foreach ($openingHours as $openingHour) {
            $openingHourText .= '- ' . $openingHour->getStart()->format('l') . ' (' . $openingHour->getStart()->format('d/m') . ') : ' . $openingHour->getStart()->format('G:i') . ' - ' . $openingHour->getEnd()->format('G:i');

            if (strlen($openingHour->getComment($language)) > 0) {
                $openingHourText .= ' (' . $openingHour->getComment($language) . ')';
            }

            $openingHourText .= "\r\n";
        }

        if ($openingHourText == '') {
            $message = str_replace('#no_opening_hours#', '', $message);
        } else {
            $message = preg_replace('/#no_opening_hours#.*#no_opening_hours#/', '', $message);
        }

        preg_match('/#expires#(.*)#expires#/', $message, $matches);
        $message = preg_replace('/#expires#.*#expires#/', '', $message);

        $list = '';
        foreach ($bookings as $booking) {
            $list .= '* ' . $booking->getArticle()->getMainArticle()->getTitle() . ' ' . ($booking->getExpirationDate() ? '(' . $matches[1] . ' ' . $booking->getExpirationDate()->format('d/m/Y') : '') . ")\r\n";
        }

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ bookings }}', $list, str_replace('{{ openingHours }}', $openingHourText, $message)))
            ->setFrom($mailAddress, $mailName)
            ->addTo($person->getEmail(), $person->getFullName())
            ->setSubject($subject);

        $sendMailsToSysadmin = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_sysadmin') == 1;
        if ($sendMailsToSysadmin) {
            $mail->addBcc(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            );
        }

        $sendMailsToCudi = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_mails_to_cudi') == 1;
        if ($sendMailsToCudi) {
            $mail->addCc($mailAddress, $mailName);
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $mailTransport->send($mail);
        }
    }
}
