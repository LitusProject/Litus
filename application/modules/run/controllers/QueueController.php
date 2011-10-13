<?php

namespace Run;

use \Run\Form\Queue\Add as AddForm;

use \Litus\Entity\Sport\Lap;
use \Litus\Entity\Sport\Runner;

use \Zend\Json\Json;

class QueueController extends \Litus\Controller\Action
{
    private $_json = null;

    public function init()
    {
        parent::init();

        $this->broker('contextSwitch')
            ->addActionContext('runner', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();

        $this->_json = new Json();
    }

    public function indexAction()
    {
        $this->_redirect('add');
    }

    public function addAction()
    {
        $form = new AddForm();

        $this->view->form = $form;
        $this->view->queueCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $runner = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Sport\Runner')
                    ->find($formData['university_identification']);

                $lap = new Lap(
                    null !== $runner ?
                        $runner :
                        new Runner(
                            $formData['university_identification'],
                            $formData['first_name'],
                            $formData['last_name']
                        )
                );
                $this->getEntityManager()->persist($lap);

                $this->view->queueCreated = true;
                $this->view->form = new AddForm();
            }
        }
    }

    public function runnerAction()
    {
        $this->_initAjax();

        $universityIdentification = $this->getRequest()->getParam('university_identification', '');

        $returnArray = array();
        if (8 == strlen($universityIdentification)) {
            $runner = $this->getEntityManager()
                ->getRepository('Litus\Entity\Sport\Runner')
                ->find($universityIdentification);

            if (null !== $runner) {
                $returnArray['firstName'] = $runner->getFirstName();
                $returnArray['lastName'] = $runner->getLastName();
            }
        }

        echo $this->_json->encode($returnArray);
    }
}

