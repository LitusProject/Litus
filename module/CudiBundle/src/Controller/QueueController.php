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
		return array(
			'socketUrl' => $this->_getSocketUrl(),
		);
	}

    public function signinAction()
	{
        $form = new SignInForm($this->getEntityManager());
        
        return array(
        	'form' => $form,
        	'socketUrl' => $this->_getSocketUrl(),
        );
    }
    
    public function addtoqueueAction ()
    {
    	$this->initAjax();
    	    	
    	$person = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\Users\Person')
    		->findOneByUsername($this->getRequest()->post()->get('username'));

    	if (null == $person) {
    		return array(
    			'result' => array('error' => 'person')
    		);
    	}
    	
    	$session = $this->getEntityManager()
    		->getRepository('CudiBundle\Entity\Sales\Session')
    		->findOpenSession();
    	
    	$queueItem = new ServingQueueItem($this->getEntityManager(), $person, $session);
    	
    	$this->getEntityManager()->persist($queueItem);
    	$this->getEntityManager()->flush();
    	
    	return array(
    		'result' => array('queueNumber' => $queueItem->getQueueNumber())
    	);
    }
    
    private function _getSocketUrl()
    {
    	$address = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('cudi.queue_socket_remote_host');
    	$port = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('cudi.queue_socket_port');
    		
    	return 'ws://' . $address . ':' . $port;
    }
}