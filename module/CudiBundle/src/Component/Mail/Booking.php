<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Component\Mail;

use CommonBundle\Entity\Users\Person,
    Zend\Mail\Message,
    Zend\Mail\Transport;

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
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \CudiBundle\Entity\Stock\Order $order The order
	 * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
	 */
    public static function sendMail(Transport $mailTransport, $bookings, Person $person, $message, $subject, $mailAddress, $mailName)
    {
        $list = '';
        foreach($bookings as $booking) {
        	$list .= '* ' . $booking->getArticle()->getMainArticle()->getTitle() . " " . ($booking->getExpirationDate() ? "(expires " . $booking->getExpirationDate()->format('d M Y') : "") . ")\r\n";
        }
        	
        $mail = new Message();
        $mail->setBody(str_replace('{{ bookings }}', $list, $message))
        	->setFrom($mailAddress, $mailName)
        	->addTo($person->getEmail(), $person->getFullName())
        	->setSubject($subject);
        	
        if ('production' == getenv('APPLICATION_ENV'))
            $mailTransport->send($mail);
    }
}