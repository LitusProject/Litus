<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\Mail;

use CommonBundle\Entity\Users\Person,
    Zend\Mail\Message,
    Zend\Mail\Transport\TransportInterface;

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
     * @param \Zend\Mail\Transport\TransportInterface $mailTransport
     * @param array $bookings
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public static function sendMail(EntityManager $entityManager, TransportInterface $mailTransport, $bookings, Person $person)
    {
        $message = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_assigned_mail');

        $subject = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_assigned_mail_subject');

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $list = '';
        foreach($bookings as $booking) {
            $list .= '* ' . $booking->getArticle()->getMainArticle()->getTitle() . " " . ($booking->getExpirationDate() ? "(expires " . $booking->getExpirationDate()->format('d M Y') : "") . ")\r\n";
        }

        $mail = new Message();
        $mail->setBody(str_replace('{{ bookings }}', $list, $message))
            ->setFrom($mailAddress, $mailName)
            ->addTo($person->getEmail(), $person->getFullName())
            ->addCc($mailAddress, $mailName)
            ->addBcc(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            )
            ->setSubject($subject);

        if ('production' == getenv('APPLICATION_ENV'))
            $mailTransport->send($mail);
    }
}
