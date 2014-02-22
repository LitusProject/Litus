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
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
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
            ->setAliases(array('cudi:warn-expire', 'cudi:expire'))
            ->setDescription('Warn users when reservations are about to expire.')
            ->addOption('mail', 'm', null, 'Send the users a warning e-mail.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> warns users when their reservations are about to expire.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();
        $interval = new DateInterval(
            $em->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.expiration_warning_interval')
        );

        $start = new DateTime();
        $start->setTime(0, 0);
        $start->add($interval);
        $end = clone $start;
        $end->add(new DateInterval('P1D'));

        $output->writeln('Looking for bookings expiring between <comment>'
            . $start->format('d M Y') . '</comment> and <comment>' . $end->format('d M Y') . '</comment>...');

        $bookings = $em->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllExpiringBetween($start, $end);

        $persons = array();
        foreach($bookings as $booking) {
            if (!isset($persons[$booking->getPerson()->getId()]))
                $persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());

            $persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
        }

        $output->writeln('Found <comment>' . count($bookings) . '</comment> bookings belonging to <comment>'
            . count($persons) . '</comment> people.');

        if ($input->getOption('mail')) {
            $output->writeln('Sending mails...');
            $count = 0;

            foreach($persons as $person) {
                $count++;
                if ($count % 3 === 0)
                    $output->write("\r" . 'Sending mail no. <comment>' . $count . '</comment>');

                Booking::sendExpireWarningMail($em, $mailTransport, $person['bookings'], $person['person']);
            }

            $output->writeln("\r" . 'Done.                                        ');
        }
    }
}
