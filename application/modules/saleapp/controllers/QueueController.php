<?php

namespace SaleApp;

use SaleApp\Form\Queue\SignIn as SignInForm;

use \Litus\Entity\Cudi\Sales\ServingQueueItem;
use \Litus\Entity\Cudi\Sales\Session;
use \Litus\Entity\Users\Person;
use \Litus\Entity\Cudi\Sales\ServingQueueStatus;
use \Litus\Entity\Cudi\Sales\PayDesk;
use \Litus\FlashMessenger\FlashMessage;
use \Litus\FlashMessenger\BootstrapFlashMessage; // this line should perhaps be in the view

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
                        "Queue number: <strong>" . $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->getQueueNumber( $queueItem ) . "</strong>"
                    )
                );

                // empty form
                $this->view->form->populate("");
            }
        }
    }

    public function overviewAction() {
        $selling = $this->getEntityManager()
                        ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueStatus')
                        ->findBy( array( 'name' => 'selling' ) )
                        ->getId();
        $collecting = $this->getEntityManager()
                        ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueStatus')
                        ->findBy( array( 'name' => 'collecting' ) )
                        ->getId();
        $signed_in = $this->getEntityManager()
                        ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueStatus')
                        ->findBy( array( 'name' => 'signed_in' ) )
                        ->getId();
        $this->view->selling_items = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session"),
                                               'status' => $selling));
        $this->view->collecting_items = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session"),
                                               'status' => $collecting));
        $this->view->signed_in_items = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session"),
                                               'status' => $signed_in));
    }

    public function dumpAction() {
        $this->view->queueItems = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session")));
    }
}

