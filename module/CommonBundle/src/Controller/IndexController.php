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

namespace CommonBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * Handles system home page.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        if ('production' == getenv('APPLICATION_ENV'))
            $this->redirect()->toUrl('http://www.vtk.be/');

        $notifications = $this->getEntityManager()
            ->getRepository('NotificationBundle\Entity\Nodes\Notification')
            ->findAllActive();

        $newsItems = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findAll();

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
                'newsItems' => $newsItems,
                'calendarItems' => $calendarItems,
                'notifications' => $notifications,
            )
        );
    }
}
