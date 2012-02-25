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

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CudiBundle\Entity\Sales\ServingQueueItem,
	CudiBundle\Form\Queue\SignIn as SignInForm;

/**
 * QueueController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueController extends \CudiBundle\Component\Controller\SaleController
{

	public function indexAction()
	{
		$this->forward()->dispatch(
			'sale',
			array(
				'controller' => 'queue',
				'action' => 'overview'
			)
		);
	}

	public function overviewAction()
	{
		$address = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.queue_socketRemoteHost');
		$port = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.queue_socketPort');

		return array(
			'socketUrl' => 'ws://' . $address . ':' . $port,
		);
	}

    public function signinAction()
	{
        $form = new SignInForm($this->getEntityManager());
        
        if($this->getRequest()->isPost()) {
        	$formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
				$person = $this->getEntityManager()
					->getRepository('CommonBundle\Entity\Users\Person')
					->findOneByUsername($formData['username']);
				
				$session = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Session')
					->findOneById($this->getParam('session'));
				
				$queueItem = new ServingQueueItem($this->getEntityManager(), $person, $session);
				
				$this->getEntityManager()->persist($queueItem);
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
					new FlashMessage(
						FlashMessage::SUCCESS,
						'Succes',
						'You are succesfully added to the queue. Your queue number is: <strong>' . $queueItem->getQueueNumber() . '</strong>'
					)
				);
				
				$this->redirect()->toRoute(
					'sale',
					array(
						'controller' => 'queue',
						'action' => 'signin',
						'session' => $session->getId(),
					)
				);
        	}
        }
        
        return array(
        	'form' => $form,
        );
    }
}