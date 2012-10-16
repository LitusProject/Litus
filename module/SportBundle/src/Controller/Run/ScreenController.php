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

namespace SportBundle\Controller\Run;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SportBundle\Entity\Lap,
    SportBundle\Entity\Runner,
    SportBundle\Form\Queue\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * ScreenController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ScreenController extends \SportBundle\Component\Controller\RunController
{
    public function indexAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
            )
        );
    }

    public function updateAction()
    {
        $this->initAjax();

        $result = array();

        if (null !== $this->_getCurrentLap()) {
            $result['currentLap'] = array(
                'runnerName' => $this->_getCurrentLap()->getRunner()->getFullName(),
                'time' => $this->_getCurrentLap()->getLapTime()->format('%i:%S')
            );
        } else {
            $result['currentLap'] = null;
        }

        $previousLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findPrevious($this->_getAcademicYear(), 5);

        $result['previousLaps'] = array();
        foreach ($previousLaps as $lap) {
            $result['previousLaps'][] = array(
                'id' => $lap->getId(),
                'runner' => $lap->getRunner()->getFullName(),
                'time' => $lap->getLapTime()->format('%i:%S')
            );
        }

        $nextLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findNext($this->_getAcademicYear(), 5);

        $result['nextLaps'] = array();
        foreach ($nextLaps as $lap) {
            $result['nextLaps'][] = array(
                'id' => $nextLap->getId(),
                'runner' => $nextLap->getRunner()->getFullName()
            );
        }

        $result['officialResults'] = $this->_getOfficialResults();

        $result['uniqueRunners'] = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->countRunners($this->_getAcademicYear());

        $result['groupsOfFriends'] = $this->_getGroupsOfFriends();

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    private function _getCurrentLap()
    {
        return $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findCurrent($this->_getAcademicYear());
    }

    private function _getGroupsOfFriends()
    {
        $groups = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Group')
            ->findAll($this->_getAcademicYear());

        $returnArray = array();
        foreach ($groups as $group) {
            $returnArray[$group->getId()]['name'] = $group->getName();
            $returnArray[$group->getId()]['points'] = 0;

            $happyHours = $group->getHappyHours();

            foreach ($group->getMembers() as $member) {
                foreach ($member->getLaps() as $lap) {
                    if (null === $lap->getEndTime())
                        continue;

                    $startTime = $lap->getStartTime()->format('H');
                    $endTime = $lap->getEndTime()->format('H');

                    $returnArray[$group->getId()]['points'] += 1;

                    for ($i = 0; isset($happyHours[$i]); $i++) {
                        if ($startTime >= substr($happyHours[$i], 0, 2) && $endTime <= substr($happyHours[$i], 2))
                            $returnArray[$group->getId()]['points'] += 1;
                    }
                }
            }
        }

        return $returnArray;
    }

    private function _getOfficialResults()
    {
        $resultPage = @simplexml_load_file(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_result_page')
        );

        $returnArray = array();
        if (false !== $resultPage) {
            $teamId = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $teamData = $resultPage->xpath('//team[@id=\'' . $teamId . '\']');

            $returnArray = array(
                'nbLaps' => $teamData[0]->rounds->__toString(),
                'position' => round($teamData[0]->position->__toString() * 100),
                'speed' => $teamData[0]->speed_kmh->__toString(),
                'behind' => $teamData[0]->behind->__toString()
            );
        }

        return $returnArray;
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );

            $next = clone $start;
            $next->add(new DateInterval('P1Y'));
            if ($next <= new DateTime())
                $start = $next;
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_remote_host');
        $port = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_port');

        return 'ws://' . $address . ':' . $port;
    }
}
