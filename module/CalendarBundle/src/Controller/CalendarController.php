<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Controller;

use DateInterval,
    DateTime,
    Zend\Date\Date,
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
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findAllActive();

        $calendarItems = array();
        foreach($events as $event) {
            $date = $event->getStartDate()->format('d-M');
            if (!isset($calendarItems[$date])) {
                $calendarItems[$date] = (object) array(
                    'date' => $event->getStartDate(),
                    'events' => array()
                );
            }
            $calendarItems[$date]->events[] = $event;
        }

        return new ViewModel(
            array(
                'calendarItems' => $calendarItems,
                'date' => new DateTime(),
            )
        );
    }

    public function viewAction()
    {
        if (!($event = $this->_getEvent()))
            return $this->notFoundAction();

        return new ViewModel(
            array(
                'event' => $event,
            )
        );
    }

    public function posterAction()
    {
        if (!($event = $this->_getEventByPoster()))
            return $this->notFoundAction();

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
        $date = $this->getParam('id');
        $first = DateTime::createFromFormat('d-m-Y H:i', '1-' . $date . ' 0:00');

        if (!$first)
            return $this->notFoundAction();

        $last = clone $first;
        $last->add(new DateInterval('P1M'));

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findAllBetween($first, $last);

        $parser = new \MarkdownExtra_Parser();

        $calendarItems = array();
        foreach($events as $event) {
            $date = $event->getStartDate()->format('d-M');
            $startDate = new Date($event->getStartDate()->format('Y/m/d H:i:s'), 'y/M/d H:m:s');
            if (!isset($calendarItems[$date])) {
                $calendarItems[$date] = (object) array(
                    'date' => $startDate->toString('d MMM'),
                    'events' => array()
                );
            }
            $calendarItems[$date]->events[] = (object) array(
                'id' => $event->getId(),
                'title' => $event->getTitle($this->getLanguage()),
                'startDate' => $startDate->toString('h:mm'),
                'content' => $parser->transform($event->getContent($this->getLanguage())),
                'url' => $this->url()->fromRoute(
                    'calendar',
                    array(
                        'action' => 'view',
                        'id' => $event->getName(),
                    )
                ),
            );
        }

        $first = new Date($first->format('Y/m/d H:i:s'), 'y/M/d H:m:s');

        return new ViewModel(
            array(
                'result' => (object) array(
                    'month' => ucfirst($first->toString('MMMM')),
                    'days' => $calendarItems,
                )
            )
        );
    }

    public function _getEvent()
    {
        if (null === $this->getParam('id'))
            return;

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findOneByName($this->getParam('id'));

        if (null === $event)
            return;

        return $event;
    }

    private function _getEventByPoster()
    {
        if (null === $this->getParam('id'))
            return;

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findOneByPoster($this->getParam('id'));

        if (null === $event)
            return;

        return $event;
    }
}
