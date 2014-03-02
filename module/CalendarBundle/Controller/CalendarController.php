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

namespace CalendarBundle\Controller;

use DateInterval,
    DateTime,
    IntlDateFormatter,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * CalendarController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CalendarController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        return new ViewModel(
            array(
                'date' => new DateTime(),
            )
        );
    }

    public function viewAction()
    {
        if (!($event = $this->_getEvent())) {
            $this->getResponse()->setStatusCode(404);

            return new ViewModel();
        }

        $hasShifts = sizeof($this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByEvent($event)) > 0;

        $ticketEvent = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneByActivity($event);

        return new ViewModel(
            array(
                'event' => $event,
                'hasShifts' => $hasShifts,
                'ticketEvent' => $ticketEvent,
            )
        );
    }

    public function posterAction()
    {
        if (!($event = $this->_getEventByPoster())) {
            $this->getResponse()->setStatusCode(404);

            return new ViewModel();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => mime_content_type($filePath . $event->getPoster()),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $event->getPoster(), 'r');
        $data = fread($handle, filesize($filePath . $event->getPoster()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function monthAction()
    {
        $this->initAjax();

        $date = $this->getParam('name');
        $first = DateTime::createFromFormat('d-m-Y H:i', '1-' . $date . ' 0:00');

        if (!$first) {
            $this->getResponse()->setStatusCode(404);

            return new ViewModel();
        }

        $last = clone $first;
        $last->add(new DateInterval('P1M'));

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllBetween($first, $last);

        $dayFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMM'
        );

        $hourFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'HH:mm'
        );

        $calendarItems = array();
        foreach ($events as $event) {
            $date = $event->getStartDate()->format('d-M');
            if (!isset($calendarItems[$date])) {
                $calendarItems[$date] = (object) array(
                    'date' => $dayFormatter->format($event->getStartDate()),
                    'events' => array()
                );
            }
            $calendarItems[$date]->events[] = (object) array(
                'id' => $event->getId(),
                'title' => $event->getTitle($this->getLanguage()),
                'startDate' => $hourFormatter->format($event->getStartDate()),
                'content' => $event->getSummary(200, $this->getLanguage()),
                'url' => $this->url()->fromRoute(
                    'calendar',
                    array(
                        'action' => 'view',
                        'name' => $event->getName(),
                    )
                ),
            );
        }

        $formatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'MMMM'
        );

        return new ViewModel(
            array(
                'result' => (object) array(
                    'month' => ucfirst($formatter->format($first)),
                    'days' => $calendarItems,
                )
            )
        );
    }

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="icalendar.ics"',
            'Content-Type' => 'text/calendar',
        ));
        $this->getResponse()->setHeaders($headers);

        $suffix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.icalendar_uid_suffix');

        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name') . ' Calendar' . PHP_EOL;
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

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive(0);

        foreach ($events as $event) {
            $result .= 'BEGIN:VEVENT' . PHP_EOL;
            $result .= 'SUMMARY:' . $event->getTitle($this->getLanguage()) . PHP_EOL;
            $result .= 'DTSTART:' . $event->getStartDate()->format('Ymd\THis') . PHP_EOL;
            if (null !== $event->getEndDate())
                $result .= 'DTEND:' . $event->getEndDate()->format('Ymd\THis') . PHP_EOL;
            $result .= 'TRANSP:OPAQUE' . PHP_EOL;
            $result .= 'LOCATION:' . $event->getLocation($this->getLanguage()) . PHP_EOL;
            $result .= 'URL:' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->url()->fromRoute(
                    'calendar',
                    array(
                        'action' => 'view',
                        'name' => $event->getName(),
                    )
                ) . PHP_EOL;
            $result .= 'CLASS:PUBLIC' . PHP_EOL;
            $result .= 'UID:' . $event->getId() . '@' . $suffix . PHP_EOL;
            $result .= 'END:VEVENT' . PHP_EOL;
        }

        $result .= 'END:VCALENDAR';

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function _getEvent()
    {
        if (null === $this->getParam('name'))
            return;

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneByName($this->getParam('name'));

        if (null === $event)
            return;

        return $event;
    }

    private function _getEventByPoster()
    {
        if (null === $this->getParam('name'))
            return;

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneByPoster($this->getParam('name'));

        if (null === $event)
            return;

        return $event;
    }
}
