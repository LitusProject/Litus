<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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

        return new ViewModel(
            array(
                'event' => $event,
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
            'Content-type' => mime_content_type($filePath . $event->getPoster()),
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
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
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
        foreach($events as $event) {
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

    public function _getEvent()
    {
        if (null === $this->getParam('name'))
            return;

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
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
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findOneByPoster($this->getParam('name'));

        if (null === $event)
            return;

        return $event;
    }
}
