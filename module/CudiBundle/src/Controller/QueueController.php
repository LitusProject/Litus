<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Controller;

use CudiBundle\Form\Queue\SignIn as SignInForm;

/**
 * QueueController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueController extends \CommonBundle\Component\Controller\ActionController
{
    
    public function indexAction()
	{
        $form = new SignInForm();
        
        if($this->getRequest()->isPost()) {
        	$formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	
        	}
        }
        
        return array(
        	'form' => $form,
        );
    }
}
/*
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
*/