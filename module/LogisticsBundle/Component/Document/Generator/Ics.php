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

namespace LogisticsBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile as TmpFile,
    Doctrine\ODM\MongoDB\DocumentManager,
    Doctrine\ORM\EntityManager,
    LogisticsBundle\Entity\Reservation\VanReservation;

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
    private $_entityManager;

    /**
     * @var DocumentManager The DocumentManager
     */
    private $_documentManager;

    /**
     * @var string
     */
    private $_token;

    /**
     * @var string
     */
    private $_suffix;

    /**
     * @param TmpFile         $file
     * @param EntityManager   $entityManager
     * @param DocumentManager $documentManager
     * @param string          $token
     */
    public function __construct(TmpFile $file, EntityManager $entityManager, DocumentManager $documentManager, $token = null)
    {
        $this->_entityManager = $entityManager;
        $this->_documentManager = $documentManager;
        $this->_token = $token;

        $this->_suffix = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.icalendar_uid_suffix');

        $file->appendContent($this->_getHeader());
        $file->appendContent($this->_getReservations());

        $file->appendContent('END:VCALENDAR');
    }

    /**
     * @return string
     */
    private function _getHeader()
    {
        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->_entityManager
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

    private function _getReservations()
    {
        $result = '';
        $reservations = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllActive();

        $person = null;
        if (null !== $this->_token) {
            $token = $this->_documentManager
                ->getRepository('LogisticsBundle\Document\Token')
                ->findOneByHash($this->_token);

            if (null !== $token) {
                $person = $token->getPerson($this->_entityManager);
            }
        }

        foreach ($reservations as $reservation) {
            if (null !== $person && $reservation->getDriver() && $reservation->getDriver()->getPerson() != $person) {
                continue;
            }

            $result .= $this->_getEvent($reservation);
        }

        return $result;
    }

    /**
     * @param  VanReservation $reservation
     * @return string
     */
    private function _getEvent(VanReservation $reservation)
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
        $result .= 'UID:' . $reservation->getId() . '@' . $this->_suffix . PHP_EOL;
        $result .= 'END:VEVENT' . PHP_EOL;

        return $result;
    }
}
