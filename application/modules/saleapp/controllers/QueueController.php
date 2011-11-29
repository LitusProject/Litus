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

        // The html parameter is the type of Ajax request. You can also use JSON or XML.
        $context = $this->broker('AjaxContext')
                ->addActionContext('dump', 'html')
                ->addActionContext('poll', 'html')
                ->initContext();
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
                               ->findOneBy( array( 'name' => 'signed_in' ) );
                $session = $this->getEntityManager()
                                ->getRepository('Litus\Entity\Cudi\Sales\Session')
                                ->findOneById($this->_getParam("session"));
                $queueItem = new ServingQueueItem();
                $queueItem->setPerson( $person );
                $queueItem->setStatus( $status );
                $queueItem->setSession( $session );
                $queueItem->setQueueNumber( $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->getQueueNumber( $session->getId() ) );

		$this->getEntityManager()->persist( $queueItem );
                $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->updatePollingData();

                $this->_addDirectFlashMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        "Queue number: <strong>" . $queueItem->getQueueNumber() . "</strong>"
                    )
                );

                // empty form
                $this->view->form->populate("");
            }
        }
    }

    public function overviewAction() {
    	$this->view->sessionId = $this->_getParam("session");
    }

    public function pollAction() {
        $itemRepo = $this->getEntityManager()
                                  ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem');
        $this->view->pollString = md5( $itemRepo->getPollingData() );
    }

    public function dumpAction() {
        $itemRepo = $this->getEntityManager()
                                  ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem');
        $statusRepo = $this->getEntityManager()
                                   ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueStatus');
        if( $this->_getParam("status") ) {

                $status = $statusRepo
                                   ->findOneBy( array( 'name' => $this->_getParam("status") ) );

             //   if( is_null( $status ) ) {
             //       throw new Exception( "unrecognized status: " . $this->_getParam("status") );
             //   }

                $queueItems = $itemRepo
                               ->findBy( array('session' => $this->_getParam("session"),
                                               'status' => $status->getId() ),
                                         array('queueNumber' => 'DESC') );
        }
        else {
                $queueItems = $itemRepo
                                  ->findBy( array('session' => $this->_getParam("session") ));
        }

        $this->view->queueItems = $queueItems;
    }
}

