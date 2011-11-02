<?php

namespace SaleApp;

use SaleApp\Form\Queue\SignIn as SignInForm;

use \Litus\Entity\Cudi\Sales\ServingQueueItem;
use \Litus\Entity\Cudi\Sales\Session;
use \Litus\Entity\Users\Person;
use \Litus\Entity\Cudi\Sales\ServingQueueStatus;
use \Litus\Entity\Cudi\Sales\PayDesk;
use \Litus\FlashMessenger\FlashMessage;

class QueueController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('sign-in');
    }

    public function signInAction()
    {
        $this->view->form = new SignInForm();

	if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if($this->view->form->isValid($formData)) {

                $person = $this->getEntityManager()
                               ->getRepository('Litus\Entity\Users\Person')
                               ->findOneBy( array( 'username' => $formData['number'] ) );

                // TODO: if $person is not valid ...
                // else,

                // create ServingQueueItem object and and persist
                $status = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueStatus')
                               ->find(1);
                $session = $this->getEntityManager()
                                ->getRepository('Litus\Entity\Cudi\Sales\Session')
                                ->findOneById($this->_getParam("session"));
                $queueItem = new ServingQueueItem();
                $queueItem->setPerson( $person );
                $queueItem->setStatus( $status );
                $queueItem->setSession( $session );

		$this->getEntityManager()->persist( $queueItem );

                $this->_addDirectFlashMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        "Queue number:" . $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->getQueueNumber( $queueItem )
                    )
                );

                // empty form
                $this->view->form->populate("");
            }
        }
    }

    public function dumpAction() {
        $this->view->queueItems = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session")));
    }
}

