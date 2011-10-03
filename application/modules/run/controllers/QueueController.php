<?php

namespace Run;

use \Run\Form\Queue\Add as AddForm;

use \Litus\Entity\Sport\Lap;
use \Litus\Entity\Sport\Runner;

class QueueController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $form = new AddForm();

        $this->view->form = $form;
        $this->view->queueCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $previousLap = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Sport\Lap')
                    ->findCurrent();

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
            }
        }
    }
}

