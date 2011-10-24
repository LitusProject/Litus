<?php

namespace Admin;

use \Admin\Form\Auth\Login as LoginForm;

class RunController extends \Litus\Controller\Action
{
	private $currentLap = null;
    private $nextLap = null;

	public function init()
	{
		parent::init();

        $this->currentLap = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findCurrent();
        $this->nextLap = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findNext();
	}
	
	public function indexAction()
	{
		$this->_forward('queue');
	}
	
	public function queueAction()
	{
        $this->view->currentLap = $this->currentLap;
        $this->view->nextLap = $this->nextLap;

        $this->view->previousLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findPrevious(5);
        $this->view->nextLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findNext(15);

        $this->view->nbLaps = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->countAll();
        
        $resultPage = $this->getEntityManager()
            ->getRepository('Litus\Entity\Config\Config')
            ->getConfigValue('sport.run_result_page');

        $resultPageContent = @simplexml_load_file($resultPage);
        
        if (false !== $resultPageContent) {
            $teamId = $this->getEntityManager()
                ->getRepository('Litus\Entity\Config\Config')
                ->getConfigValue('sport.run_team_id');
            
            $teamData = $resultPageContent->xpath('//team[@id=\'' . $teamId . '\']');

            $this->view->nbOfficialLaps = $teamData[0]->rounds->__toString();
        } else {
            $this->view->nbOfficialLaps = false;
        }
	}

    public function startAction()
    {
        $this->broker('viewRenderer')->setNoRender();

        if (null !== $this->currentLap)
            $this->currentLap->stop();

        if (null !== $this->nextLap)
            $this->nextLap->start();

        $this->_redirect('queue');
    }

    public function stopAction()
    {
        $this->broker('viewRenderer')->setNoRender();

        if (null !== $this->currentLap)
            $this->currentLap->stop();

        $this->_redirect('queue');
    }

    public function deleteAction()
    {
        $lap = $this->getEntityManager()
            ->getRepository('Litus\Entity\Sport\Lap')
            ->findOneById($this->getRequest()->getParam('id'));

        $this->view->lapDeleted = false;

        if (null === $this->getRequest()->getParam('confirm')) {
            $this->view->lap = $lap;
        } else {
            if (1 == $this->getRequest()->getParam('confirm')) {
                $this->getEntityManager()->remove($lap);
                $this->view->lapDeleted = true;
            } else {
                $this->_redirect('queue');
            }
        }
    }
}
