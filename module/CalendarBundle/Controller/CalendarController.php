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

use CalendarBundle\Component\Document\Generator\Ics as IcsGenerator,
    CalendarBundle\Entity\Node\Event,
    CommonBundle\Component\Util\File\TmpFile,
    DateInterval,
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
        if (!($event = $this->getEventEntity())) {
            return $this->notFoundAction();
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
        if (!($event = $this->getEventEntityByPoster())) {
            return $this->notFoundAction();
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
            return $this->notFoundAction();
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

        $calendarItems = array();
        foreach ($events as $event) {
            $date = $event->getStartDate()->format('d-M');
            if (!isset($calendarItems[$date])) {
                $calendarItems[$date] = (object) array(
                    'day' => ucfirst($event->getStartDate()->format('d')),
                    'month' => $monthFormatter->format($event->getStartDate()),
                    'events' => array(),
                );
            }

            if (null !== $event->getEndDate()) {
                if ($event->getEndDate()->format('d/M/Y') == $event->getStartDate()->format('d/M/Y')) {
                    $fullTime = $hourFormatter->format($event->getStartDate()) . ' - ' . $hourFormatter->format($event->getEndDate());
                } else {
                    $fullTime = $dayFormatter->format($event->getStartDate()) . ' ' . $hourFormatter->format($event->getStartDate()) . ' - ' . $dayFormatter->format($event->getEndDate()) . ' ' . $hourFormatter->format($event->getEndDate());
                }
            } else {
                $fullTime = $hourFormatter->format($event->getStartDate());
            }

            $calendarItems[$date]->events[] = (object) array(
                'id' => $event->getId(),
                'title' => $event->getTitle($this->getLanguage()),
                'startDate' => $hourFormatter->format($event->getStartDate()),
                'summary' => $event->getSummary(100, $this->getLanguage()),
                'content' => $event->getSummary(200, $this->getLanguage()),
                'fullTime' => $fullTime,
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
                ),
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

        $icsFile = new TmpFile();
        new IcsGenerator($icsFile, $this->getEntityManager(), $this->getLanguage(), $this->getRequest(), $this->url());

        return new ViewModel(
            array(
                'result' => $icsFile->getContent(),
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
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event', 'name', 'name');

        if (!($event instanceof Event)) {
            return;
        }

        return $event;
    }
}
