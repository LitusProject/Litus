<?php

namespace CalendarBundle\Controller;

use CalendarBundle\Component\Document\Generator\Ics as IcsGenerator;
use CalendarBundle\Entity\Node\Event;
use CommonBundle\Component\Util\File\TmpFile;
use DateInterval;
use DateTime;
use IntlDateFormatter;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

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
        $event = $this->getEventEntity();
        if ($event === null) {
            return $this->notFoundAction();
        }

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByEvent($event);

        $timeslots = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllActiveByEvent($event);

        $ticketEvent = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneByActivity($event);

        return new ViewModel(
            array(
                'event'                 => $event,
                'hasShifts'             => count($shifts) > 0,
                'hasRegistrationShifts' => count($timeslots) > 0,
                'ticketEvent'           => $ticketEvent,
            )
        );
    }

    public function posterAction()
    {
        $event = $this->getEventEntityByPoster();
        if ($event === null) {
            return $this->notFoundAction();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => mime_content_type($filePath . $event->getPoster()),
            )
        );
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
            return $this->notFoundAction();
        }

        $last = clone $first;
        $last->add(new DateInterval('P1M'));

        $currentDate = new DateTime();

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllBetweenAndNotHidden($first, $last);

        $dayFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMM'
        );

        $monthFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'LLL'
        );

        $hourFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'HH:mm'
        );

        $weekdayFormatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'EEE'
        );

        $calendarItems = array();
        foreach ($events as $event) {
            if ($event->getEndDate() >= $currentDate) {
                $date = $event->getStartDate()->format('d-M');
                if (!isset($calendarItems[$date])) {
                    $calendarItems[$date] = (object) array(
                        'weekday' => $weekdayFormatter->format($event->getStartDate()),
                        'day'     => ucfirst($event->getStartDate()->format('d')),
                        'month'   => $monthFormatter->format($event->getStartDate()),
                        'events'  => array(),
                    );
                }

                if ($event->getEndDate() !== null) {
                    if ($event->getEndDate()->format('d/M/Y') == $event->getStartDate()->format('d/M/Y')) {
                        $fullTime = $hourFormatter->format($event->getStartDate()) . ' - ' . $hourFormatter->format($event->getEndDate());
                    } else {
                        $fullTime = $dayFormatter->format($event->getStartDate()) . ' ' . $hourFormatter->format($event->getStartDate()) . ' - ' . $dayFormatter->format($event->getEndDate()) . ' ' . $hourFormatter->format($event->getEndDate());
                    }
                } else {
                    $fullTime = $hourFormatter->format($event->getStartDate());
                }

                $calendarItems[$date]->events[] = (object) array(
                    'id'        => $event->getId(),
                    'title'     => $event->getTitle($this->getLanguage()),
                    'startDate' => $hourFormatter->format($event->getStartDate()),
                    'summary'   => $event->getSummary(100, $this->getLanguage()),
                    'content'   => $event->getSummary(200, $this->getLanguage()),
                    'fullTime'  => $fullTime,
                    'url'       => $this->url()->fromRoute(
                        'calendar',
                        array(
                            'action' => 'view',
                            'name'   => $event->getName(),
                        )
                    ),
                    'poster'    => $this->url()->fromRoute(
                        'calendar',
                        array(
                            'action' => 'poster',
                            'name'   => $event->getPoster(),
                        )
                    ),
                );
            }
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
                    'days'  => $calendarItems,
                ),
            )
        );
    }

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="icalendar.ics"',
                'Content-Type'        => 'text/calendar',
            )
        );
        $this->getResponse()->setHeaders($headers);

        $icsFile = new TmpFile();
        new IcsGenerator($icsFile, $this->getEntityManager(), $this->getLanguage(), $this->getRequest(), $this->url());

        return new ViewModel(
            array(
                'result' => $icsFile->getContent(),
            )
        );
    }

    public function eerstejaarsCalendarAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllEerstejaarsAndActiveAndNotHidden();

        $calendarItems = array();
        foreach ($events as $event) {
            $calendarItems[$event->getId()] = $event;
        }

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'calendarItems' => $calendarItems,
            )
        );
    }

    public function internationalCalendarAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllInternationalAndActiveAndNotHidden();

        $calendarItems = array();
        foreach ($events as $event) {
            $calendarItems[$event->getId()] = $event;
        }

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'calendarItems' => $calendarItems,
            )
        );
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event', 'name', 'name');

        if (!($event instanceof Event)) {
            return;
        }

        return $event;
    }

    /**
     * @return Event|null
     */
    private function getEventEntityByPoster()
    {
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event', 'name', 'poster');

        if (!($event instanceof Event)) {
            return;
        }

        return $event;
    }
}
