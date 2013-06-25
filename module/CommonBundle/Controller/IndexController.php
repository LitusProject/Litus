<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
        $notifications = $this->getEntityManager()
            ->getRepository('NotificationBundle\Entity\Nodes\Notification')
            ->findAllActive();

        $bookings = null;
        if (null !== $this->getAuthentication()->getPersonObject()) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllOpenByPerson($this->getAuthentication()->getPersonObject());

            foreach ($bookings as $key => $booking) {
                if ('assigned' != $booking->getStatus())
                    unset($bookings[$key]);
            }

            if (0 == count($bookings))
                $bookings = null;
        }

        $newsItems = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findNbSite(5);

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
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

        $cudi = array();
        $cudi['currentOpeningHour'] = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour')
            ->findCurrent();

        $sessions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Session')
                ->findOpen();
        if (sizeof($sessions) == 1) {
            $cudi['currentSession'] = $sessions[0];

            $cudi['currentStudents'] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\QueueItem')
                ->findNbBySession($cudi['currentSession']);
        }

        $cudi['openingHours'] = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour')
            ->findWeekFromNow();

        return new ViewModel(
            array(
                'notifications' => $notifications,
                'bookings' => $bookings,
                'newsItems' => $newsItems,
                'calendarItems' => $calendarItems,
                'sportInfo' => $this->_getSportResults(),
                'cudi' => $cudi,
            )
        );
    }

    private function _getSportResults()
    {
        $showInfo = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.sport_info_on_homepage');

        if ($showInfo != '1')
            return null;

        $cacheDir = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.cache_xml_path');

        $fileContents = @file_get_contents($cacheDir . 'ulyssis.xml');

        $resultPage = null;
        if (false !== $fileContents)
            $resultPage = simplexml_load_string($fileContents);

        $returnArray = array();
        if (null !== $resultPage) {
            $teamId = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $teamData = $resultPage->xpath('//team[@id=\'' . $teamId . '\']');

            $returnArray = array(
                'nbLaps' => $teamData[0]->rounds->__toString(),
                'position' => round($teamData[0]->position->__toString() * 100),
                'speed' => $teamData[0]->speed_kmh->__toString(),
                'behind' => $teamData[0]->behind->__toString(),
                'currentLap' => $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Lap')
                    ->findCurrent($this->getCurrentAcademicYear()),
            );
        }

        return $returnArray;
    }
}
