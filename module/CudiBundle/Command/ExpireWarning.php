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

namespace CudiBundle\Command;

use CudiBundle\Component\Mail\Booking,
    RuntimeException,
    DateTime,
    DateInterval;

/**
 * Sends expiry warnings to users
 */
class ExpireWarning extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('cudi:expire-warning')
            ->setAliases(array('cudi:warn-expire'))
            ->setDescription('Warn users when reservations are about to expire.')
            ->addOption('mail', 'm', null, 'Send the users a warning e-mail.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command warns users when their reservations are about to expire.
EOT
        );
    }

    protected function executeCommand()
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

        $this->writeln('Looking for bookings expiring between <comment>'
            . $start->format('d M Y') . '</comment> and <comment>' . $end->format('d M Y') . '</comment>...');

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllExpiringBetween($start, $end);

        $persons = array();
        foreach ($bookings as $booking) {
            if (!isset($persons[$booking->getPerson()->getId()]))
                $persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());

            $persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
        }

        $this->writeln('Found <comment>' . count($bookings) . '</comment> bookings belonging to <comment>'
            . count($persons) . '</comment> people.');

        if ($this->getOption('mail')) {
            $this->write('Sending mails...');
            $count = 0;

            foreach ($persons as $person) {
                $count++;
                if ($count % 3 === 0)
                    $this->write("\r" . 'Sending mail no. <comment>' . $count . '</comment>');

                Booking::sendExpireWarningMail($this->getEntityManager(), $this->getMailTransport(), $person['bookings'], $person['person']);
            }

            $this->writeln("\r" . 'Done.                                        ');
        }
    }

    protected function getLogName()
    {
        return 'ExpireWarning';
    }
}
