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
 * Send warning mail before expiring a booking.
 *
 * Usage:
 * --run|-r     Run this script
 * --mail|-m    Send the mails
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.em.orm_default');
$mailTransport = $application->getServiceManager()->get('mail_transport');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

$rules = array(
    'run|r'   => 'Run the script',
    'mail|m'  => 'Send the mails',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $interval = new \DateInterval(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.expiration_warning_interval')
    );

    $start = new \DateTime();
    $start->setTime(0, 0);
    $start->add($interval);
    $end = clone $start;
    $end->add(new \DateInterval('P1D'));

    echo 'Sending mails to bookings expiring between ' . $start->format('d M Y') . ' and ' . $end->format('d M Y') . '...' . PHP_EOL;

    $bookings = $em->getRepository('CudiBundle\Entity\Sale\Booking')
        ->findAllExpiringBetween($start, $end);

    $persons = array();
    foreach ($bookings as $booking) {
        if (!isset($persons[$booking->getPerson()->getId()]))
            $persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());

        $persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
    }

    $counter = 0;
    if (isset($opts->m)) {
        foreach ($persons as $person) {
            \CudiBundle\Component\Mail\Booking::sendExpireWarningMail($em, $mailTransport, $person['bookings'], $person['person']);
            $counter++;
        }
    }
    echo 'Send ' . $counter . ' mails' . PHP_EOL;
}
