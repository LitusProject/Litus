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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
	Zend\View\Model\ViewModel;

/**
 * MailController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */	
class MailController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
	public function manageAction()
	{	
	    return new ViewModel(
	        array(
    	    	'university' => UniversityStatus::$possibleStatuses,
    	    	'organization' => OrganizationStatus::$possibleStatuses,
    	    )
	    );
	}
	
	public function sendAction()
	{
	    return new ViewModel();
	}
    
    private function _getUniversityStatus()
	{
		if (null === $this->getParam('group')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No university status given to send a mail to!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_mail',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		};
		
		$status = $this->getParam('group');
		
		if (!array_key_exists($status, UniversityStatus::$possibleStatuses)) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'The given university status was not valid!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_mail',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $user;
	}    
	
	private function _getOrganizationStatus()
	{
		if (null === $this->getParam('group')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No organization status given to send a mail to!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_mail',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		};
		
		$status = $this->getParam('group');
		
		if (!array_key_exists($status, OrganizationStatus::$possibleStatuses)) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'The given organization status was not valid!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_mail',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $user;
	}
}