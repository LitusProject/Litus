<?php

namespace CudiBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OpeningHourController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function weekAction()
    {
        try {
            $id = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.opening_hours_page');

            $link = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findOneById($id);

            if ($link !== null) {
                $page = $link->getParent();
            }
        } catch (\Throwable $e) {
            // No page info available
        }

        if (isset($page)) {
            $submenu = $this->buildSubmenu($page);
            if (count($submenu) == 0 && $page->getParent() !== null) {
                $submenu = $this->buildSubmenu($page->getParent());
            }
        }

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findCurrentWeek();

        $start = new DateTime();
        $start->setTime(0, 0);
        if ($start->format('N') > 5) {
            $start->add(new DateInterval('P' . (8 - $start->format('N')) . 'D'));
        } else {
            $start->sub(new DateInterval('P' . ($start->format('N') - 1) . 'D'));
        }

        $startHour = 12;
        $endHour = 20;

        $week = array();
        $openingHoursArray = array();
        $start->sub(new DateInterval('P1D'));
        for ($i = 0; $i < 5; $i ++) {
            $start->add(new DateInterval('P1D'));
            $week[] = clone $start;
            $openingHoursArray[$i] = array();
        }

        foreach ($openingHours as $openingHour) {
            if ($openingHour->getStart()->format('H') < $startHour) {
                $startHour = $openingHour->getStart()->format('H');
            }

            if ($openingHour->getEnd()->format('H') > $endHour) {
                $endHour = $openingHour->getEnd()->format('H');
            }

            $openingHoursArray[$openingHour->getStart()->format('N') - 1][] = $openingHour;
        }

        return new ViewModel(
            array(
                'openingHours'         => $openingHours,
                'openingHoursTimeline' => $openingHoursArray,
                'week'                 => $week,
                'startHour'            => $startHour,
                'endHour'              => $endHour,
                'submenu'              => $submenu ?? null,
                'page'                 => $link ?? null,
            )
        );
    }
}
