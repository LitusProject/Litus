<?php

namespace CudiBundle\Command;

use CudiBundle\Component\Mail\Booking;
use DateInterval;
use DateTime;

/**
 * Sends expiry warnings to users
 */
class ExpireWarning extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('cudi:expire-warning')
            ->setDescription('Warn users when reservations are about to expire')
            ->addOption('mail', 'm', null, 'Send the users a warning mail');
    }

    protected function invoke()
    {
        $interval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.expiration_warning_interval')
        );

        $start = new DateTime();
        $start->setTime(0, 0);
        $start->add($interval);
        $end = clone $start;
        $end->add(new DateInterval('P1D'));

        $this->writeln('Searching for bookings expiring between <comment>' . $start->format('d M Y') . '</comment> and <comment>' . $end->format('d M Y') . '</comment>...');

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllExpiringBetween($start, $end);

        $persons = array();
        foreach ($bookings as $booking) {
            if (!isset($persons[$booking->getPerson()->getId()])) {
                $persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());
            }

            $persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
        }

        $this->writeln('Found <comment>' . count($bookings) . '</comment> bookings belonging to <comment>' . count($persons) . '</comment> people.');

        $sendMails = $this->getOption('mail');
        if ($sendMails && getenv('APPLICATION_ENV') == 'development') {
            $sendMails = false;
            $this->writeln('<error>The mails will not be sent because the application is running in development mode!</error>');
        }

        if ($sendMails) {
            foreach ($persons as $person) {
                Booking::sendExpireWarningMail(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    $person['bookings'],
                    $person['person']
                );
            }
        }

        if ($sendMails) {
            $this->writeln('<comment>' . count($persons) . '</comment> mails have been sent');
        } else {
            $this->writeln('<comment>' . count($persons) . '</comment> mails would have been sent');
        }
    }
}
