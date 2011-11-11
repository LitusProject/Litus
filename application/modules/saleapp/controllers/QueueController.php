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
                ->clearHeaders( 'html' )
		->setAutoDisableLayout( true )
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
    	$this->view->sessionId = $this->_getParam("session");
    }

    public function dumpAction() {
        $this->view->queueItems = $this->getEntityManager()
                               ->getRepository('\Litus\Entity\Cudi\Sales\ServingQueueItem')
                               ->findBy( array('session' => $this->_getParam("session")));
    }
}

