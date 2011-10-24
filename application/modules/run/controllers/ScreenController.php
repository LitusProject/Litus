<?php

namespace Run;

use \Run\Form\Group\Add as AddForm;

use \Litus\Entity\Sport\Group;
use \Litus\Entity\Sport\Runner;

use \Zend\Dom\Query;
use \Zend\Json\Json;

class ScreenController extends \Litus\Controller\Action
{
    private $_json = null;

    private $currentLap = null;

    public function init()
    {
        parent::init();

        $this->broker('contextSwitch')
            ->addActionContext('currentlap', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();

        $this->broker('layout')->disableLayout();

        $this->_json = new Json();

        $this->currentLap = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findCurrent();
    }

    private function _getOfficialResults()
    {
        $resultPage = $this->getEntityManager()
            ->getRepository('Litus\Entity\Config\Config')
            ->getConfigValue('sport.run_result_page');

        $resultPageContent = @simplexml_load_file($resultPage);

        if (false !== $resultPageContent) {
            $teamId = $this->getEntityManager()
                ->getRepository('Litus\Entity\Config\Config')
                ->getConfigValue('sport.run_team_id');

            $teamData = $resultPageContent->xpath('//team[@id=\'' . $teamId . '\']');

            return array(
                'nbLaps' => $teamData[0]->rounds->__toString(),
                'position' => $teamData[0]->position->__toString(),
                'speed' => $teamData[0]->speed_kmh->__toString()
            );
        } else {
            return false;
        }
    }

    private function _getGroupsOfFriends()
    {
        $groups = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Group')
            ->findAll();

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

    public function indexAction()
    {
        $this->view->currentLap = $this->currentLap;

        $this->view->officialResults = $this->_getOfficialResults();

        $this->view->uniqueRunners = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->countRunners();
    }

    public function updateAction()
    {
        $this->_initAjax();

        $returnArray = array();

        if (null !== $this->currentLap) {
            $now = new \DateTime();

            $returnArray['currentLap'] = array(
                'runnerName' => $this->currentLap->getRunner()->getFullName(),
                'time' => $this->currentLap->getLapTime()->format('%i:%S')
            );
        } else {
            $returnArray['currentLap'] = false;
        }

        $previousLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findPrevious(5);

        $returnArray['previousLaps'] = array();
        foreach ($previousLaps as $previousLap) {
            $returnArray['previousLaps'][] = array(
                'id' => $previousLap->getId(),
                'runner' => $previousLap->getRunner()->getFullName(),
                'time' => $previousLap->getLapTime()->format('%i:%S')
            );
        }

        $nextLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findNext(5);

        $returnArray['nextLaps'] = array();
        foreach ($nextLaps as $nextLap) {
            $returnArray['nextLaps'][] = array(
                'id' => $nextLap->getId(),
                'runner' => $nextLap->getRunner()->getFullName()
            );
        }

        $returnArray['officialResults'] = $this->_getOfficialResults();

        $returnArray['uniqueRunners'] = $this->view->uniqueRunners = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->countRunners();

        $returnArray['groupsOfFriends'] = $this->_getGroupsOfFriends();

        echo $this->_json->encode($returnArray);
    }
}