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

    private function _getNbOfficialLaps()
    {
        $resultPage = $this->getEntityManager()
            ->getRepository('Litus\Entity\Config\Config')
            ->getConfigValue('sport.run_result_page');

        $queryContents = @file_get_contents($resultPage);

        if (false !== $queryContents) {
            $teamName = $this->getEntityManager()
                ->getRepository('Litus\Entity\Config\Config')
                ->getConfigValue('sport.run_team_name');

            $domQuery = new Query($queryContents);
            $childNodes = $domQuery->execute('tr');

            foreach ($childNodes as $childNode) {
                if (0 == $childNode->getElementsByTagName('td')->length)
                    continue;

                $nodeTeamName = $childNode->getElementsByTagName('td')->item(2)->textContent;

                if (null !== $nodeTeamName && $nodeTeamName == $teamName) {
                    return $childNode->getElementsByTagName('td')->item(3)->textContent;
                }
            }
        } else {
            return false;
        }
    }

    public function indexAction()
    {
        $this->view->currentLap = $this->currentLap;

        $this->view->nbOfficialLaps = $this->_getNbOfficialLaps();

        $this->view->uniqueRunners = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->countRunners();
    }

    public function updateAction()
    {
        $this->_initAjax();

        $return = array();

        if (null !== $this->currentLap) {
            $now = new \DateTime();

            $return['currentLap'] = array(
                'runnerName' => $this->currentLap->getRunner()->getFullName(),
                'time' => $this->currentLap->getLapTime()->format('%i:%S')
            );
        } else {
            $return['currentLap'] = false;
        }

        $previousLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findPrevious(5);

        $return['previousLaps'] = array();
        foreach ($previousLaps as $previousLap) {
            $return['previousLaps'][] = array(
                'id' => $previousLap->getId(),
                'runner' => $previousLap->getRunner()->getFullName(),
                'time' => $previousLap->getLapTime()->format('%i:%S')
            );
        }

        $nextLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findNext(5);

        $return['nextLaps'] = array();
        foreach ($nextLaps as $nextLap) {
            $return['nextLaps'][] = array(
                'id' => $nextLap->getId(),
                'runner' => $nextLap->getRunner()->getFullName()
            );
        }

        $return['nbOfficialLaps'] = $this->_getNbOfficialLaps();
        

        echo $this->_json->encode($return);
    }
}