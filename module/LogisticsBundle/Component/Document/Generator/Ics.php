<?php

namespace LogisticsBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile;
use Doctrine\ORM\EntityManager;
use LogisticsBundle\Entity\Reservation\Van as VanReservation;

/**
 * Ics
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Ics
{
    /**
     * @var EntityManager The EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param TmpFile       $file
     * @param EntityManager $entityManager
     * @param string|null   $token
     */
    public function __construct(TmpFile $file, EntityManager $entityManager, $token = null)
    {
        $this->entityManager = $entityManager;
        $this->token = $token;

        $this->suffix = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.icalendar_uid_suffix');

        $file->appendContent($this->getHeader());
        $file->appendContent($this->getReservations());

        $file->appendContent('END:VCALENDAR');
    }

    /**
     * @return string
     */
    private function getHeader()
    {
        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name') . ' Logistics' . PHP_EOL;
        $result .= 'PRODID:-//lituscal//NONSGML v1.0//EN' . PHP_EOL;
        $result .= 'CALSCALE:GREGORIAN' . PHP_EOL;
        $result .= 'METHOD:PUBLISH' . PHP_EOL;
        $result .= 'X-WR-TIMEZONE:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:VTIMEZONE' . PHP_EOL;
        $result .= 'TZID:Europe/Brussels' . PHP_EOL;
        $result .= 'X-LIC-LOCATION:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:DAYLIGHT' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0100' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0200' . PHP_EOL;
        $result .= 'TZNAME:CEST' . PHP_EOL;
        $result .= 'DTSTART:19700329T020000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:DAYLIGHT' . PHP_EOL;
        $result .= 'BEGIN:STANDARD' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0200' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0100' . PHP_EOL;
        $result .= 'TZNAME:CET' . PHP_EOL;
        $result .= 'DTSTART:19701025T030000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:STANDARD' . PHP_EOL;
        $result .= 'END:VTIMEZONE' . PHP_EOL;

        return $result;
    }

    private function getReservations()
    {
        $result = '';
        $reservations = $this->entityManager
            ->getRepository('LogisticsBundle\Entity\Reservation\Van')
            ->findAllActive();

        $person = null;
        if ($this->token !== null) {
            $token = $this->entityManager
                ->getRepository('LogisticsBundle\Entity\Token')
                ->findOneByHash($this->token);

            if ($token !== null) {
                $person = $token->getPerson();
            }
        }

        foreach ($reservations as $reservation) {
            if ($person !== null && $reservation->getDriver() && $reservation->getDriver()->getPerson() != $person) {
                continue;
            }

            $result .= $this->getEvent($reservation);
        }

        return $result;
    }

    /**
     * @param  VanReservation $reservation
     * @return string
     */
    private function getEvent(VanReservation $reservation)
    {
        $summary = array();
        if (strlen($reservation->getLoad()) > 0) {
            $summary[] = str_replace("\n", '', $reservation->getLoad());
        }

        if (strlen($reservation->getAdditionalInfo()) > 0) {
            $summary[] = str_replace("\n", '', $reservation->getAdditionalInfo());
        }

        $result = 'BEGIN:VEVENT' . PHP_EOL;
        $result .= 'SUMMARY:' . $reservation->getReason() . PHP_EOL;
        $result .= 'DTSTART:' . $reservation->getStartDate()->format('Ymd\THis') . PHP_EOL;
        $result .= 'DTEND:' . $reservation->getEndDate()->format('Ymd\THis') . PHP_EOL;
        if ($reservation->getDriver()) {
            $result .= 'ORGANIZER;CN="' . $reservation->getDriver()->getPerson()->getFullname() . '":MAILTO:' . $reservation->getDriver()->getPerson()->getEmail() . PHP_EOL;
        }
        if ($reservation->getPassenger()) {
            $result .= 'ATTENDEE;CN="' . $reservation->getPassenger()->getFullname() . '":MAILTO:' . $reservation->getPassenger()->getEmail() . PHP_EOL;
        }
        $result .= 'DESCRIPTION:' . implode(' - ', $summary) . PHP_EOL;
        $result .= 'TRANSP:OPAQUE' . PHP_EOL;
        $result .= 'CLASS:PUBLIC' . PHP_EOL;
        $result .= 'UID:' . $reservation->getId() . '@' . $this->suffix . PHP_EOL;
        $result .= 'END:VEVENT' . PHP_EOL;

        return $result;
    }
}
