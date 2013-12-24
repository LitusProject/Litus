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
 * IndexController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $notifications = $this->getEntityManager()
            ->getRepository('NotificationBundle\Entity\Node\Notification')
            ->findAllActive();

        $bookings = null;
        if (null !== $this->getAuthentication()->getPersonObject()) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllOpenByPerson($this->getAuthentication()->getPersonObject());

            foreach ($bookings as $key => $booking) {
                if ('assigned' != $booking->getStatus())
                    unset($bookings[$key]);
            }

            if (0 == count($bookings))
                $bookings = null;
        }

        $newsItems = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findNbActive(5);

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
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrent();

        $sessions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findOpen();
        if (sizeof($sessions) == 1) {
            $cudi['currentSession'] = $sessions[0];

            $cudi['currentStudents'] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                ->findNbBySession($cudi['currentSession']);
        }

        $cudi['openingHours'] = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findPeriodFromNow('P14D');

        $enablePiwik = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.enable_piwik');

        $piwik = null;
        if ('development' != getenv('APPLICATION_ENV') && $enablePiwik) {
            $piwik = array(
                'url' => parse_url(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('common.piwik_api_url'),
                    PHP_URL_HOST
                ),
                'site_id' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_id_site')
            );
        }

        $myShifts = null;
        if ($this->getAuthentication()->getPersonObject()) {
            $myShifts = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }

        return new ViewModel(
            array(
                'bookings' => $bookings,
                'calendarItems' => $calendarItems,
                'cudi' => $cudi,
                'newsItems' => $newsItems,
                'notifications' => $notifications,
                'piwik' => $piwik,
                'sportInfo' => $this->_getSportResults(),
                'myShifts' => $myShifts,
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

        $fileContents = @file_get_contents('data/cache/' . md5('run_result_page'));

        $resultPage = null;
        if (false !== $fileContents)
            $resultPage = (array) json_decode($fileContents);

        $nbOfficialLaps = null;
        if (null !== $resultPage) {
            $teamId = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $currentPlace = null;
            $teamData = null;
            foreach ($resultPage['teams'] as $place => $team) {
                if ($team[0] == $teamId) {
                    $currentPlace = $place;
                    $teamData = $team;
                }
            }

            if (null !== $teamData) {
                $behind = 0;
                if (null !== $currentPlace && $currentPlace > 0) {
                    $firstData = $resultPage['teams'][0];
                    $behind = round(($firstData[2] + $firstData[3]) - ($teamData[2] + $teamData[3]), 2);
                }

                $lapsPerSecond = 1/($resultPage['lap']/($teamData[4]/3.6));

                $returnArray = array(
                    'nbLaps' => $teamData[2],
                    'position' => round($teamData[3] * 100),
                    'speed' => round($teamData[4], 2),
                    'behind' => $behind,
                    'currentLap' => $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Lap')
                        ->findCurrent($this->getCurrentAcademicYear()),
                );
            }
        }

        return $returnArray;
    }
}
