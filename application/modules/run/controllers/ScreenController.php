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

    public function indexAction()
    {
        $this->view->currentLap = $this->currentLap;

        $resultPage = $this->getEntityManager()
            ->getRepository('Litus\Entity\Config\Config')
            ->getConfigValue('sport.run_result_page');

        $queryContents = @file_get_contents($resultPage);

        if (false !== $queryContents) {
            $teamName = $this->getEntityManager()
                ->getRepository('Litus\Entity\Config\Config')
                ->getConfigValue('sport.run_team_name');

            $domQuery = new Query($queryContents);
            $childNodes = $this->view->nbOfficialLaps = $domQuery->execute('tr');

            foreach ($childNodes as $childNode) {
                if (0 == $childNode->getElementsByTagName('td')->length)
                    continue;

                $nodeTeamName = $childNode->getElementsByTagName('td')->item(2)->textContent;

                if (null !== $nodeTeamName && $nodeTeamName == $teamName) {
                    $this->view->nbOfficialLaps = $childNode->getElementsByTagName('td')->item(3)->textContent;
                }
            }
        } else {
            $this->view->nbOfficialLaps = false;
        }

        $this->view->uniqueRunners = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->countRunners();
    }

    public function currentlapAction()
    {
        $this->_initAjax();

        if (null !== $this->currentLap) {
            $now = new \DateTime();

            $return = array(
                'runnerName' => $this->currentLap->getRunner()->getFullName(),
                'time' => $this->currentLap->getLapTime()->format('%i:%S')
            );
        } else {
            $return = false;
        }
        
        echo $this->_json->encode($return);
    }
}