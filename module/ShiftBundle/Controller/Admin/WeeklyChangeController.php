<?php

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * WeeklyChangeController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class WeeklyChangeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        $ranking = array();

        $hoursPerBlock = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.hours_per_shift');

        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        $volunteersNow = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
            ->findAllCountsByAcademicYear($academicYear, $hoursPerBlock, $points_enabled);

        $changeInterval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.weekly_change_interval')
        );

        $volunteersThen = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
            ->findAllCountsAtTimeByAcademicYear($academicYear, $hoursPerBlock, $points_enabled, date_sub(new DateTime(), $changeInterval));

        $oldVolunteers = array();
        for ($i = 0; isset($rankingCriteria[$i]); $i++) {
            foreach ($volunteersNow as $volunteer) {
                foreach ($volunteersThen as $volunteerThen) {
                    if ($volunteer['id'] === $volunteerThen['id']) {
                        array_push($oldVolunteers, $volunteer['id']);
                        //check which ranking the person IS in
                        $isLast = !isset($rankingCriteria[$i + 1]);
                        if ($volunteer['resultCount'] >= $rankingCriteria[$i]['limit'] && ($isLast || $volunteer['resultCount'] < $rankingCriteria[$i + 1]['limit'])) {
                            //check which ranking the person WAS in
                            $previous_i = null;
                            for ($a = 0; isset($rankingCriteria[$a]); $a++) {
                                $isLast = !isset($rankingCriteria[$a + 1]);
                                if ($volunteerThen['resultCount'] >= $rankingCriteria[$a]['limit'] && ($isLast || $volunteerThen['resultCount'] < $rankingCriteria[$a + 1]['limit'])) {
                                    $previous_i = $a;
                                }
                            }

                            $person = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                                ->findOneById($volunteer['id']);

                            $ranking[$rankingCriteria[$i]['name']][] = array(
                                'person'          => $person,
                                'resultCount'     => $volunteer['resultCount'],
                                'rank'            => $rankingCriteria[$i]['name'],
                                'rankThen'        => $previous_i === null ? 'none' : $rankingCriteria[$previous_i + 1]['name'],
                                'resultCountThen' => $volunteerThen['resultCount'],
                            );
                        }
                    }
                }
                if (!in_array($volunteer['id'], $oldVolunteers)) {
                    $isLast = !isset($rankingCriteria[$i + 1]);
                    if ($volunteer['resultCount'] >= $rankingCriteria[$i]['limit'] && ($isLast || $volunteer['resultCount'] < $rankingCriteria[$i + 1]['limit'])) {
                        $person = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person\Academic')
                            ->findOneById($volunteer['id']);

                        $ranking[$rankingCriteria[$i]['name']][] = array(
                            'person'          => $person,
                            'resultCount'     => $volunteer['resultCount'],
                            'rank'            => $rankingCriteria[$i]['name'],
                            'rankThen'        => 'none',
                            'resultCountThen' => 0,
                        );
                    }
                }
            }
        }

        $rewards_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.rewards_enabled');
        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        return new ViewModel(
            array(
                'activeAcademicYear'   => $academicYear,
                'academicYears'        => $academicYears,
                'ranking'              => $ranking,
                'hoursPerBlock'        => $hoursPerBlock,
                'rewards_enabled'      => $rewards_enabled,
                'points_enabled'       => $points_enabled,
                'weeklyChangeInterval' => $changeInterval->format('%d days'),
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if ($academicYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $academicYear;
    }
}
