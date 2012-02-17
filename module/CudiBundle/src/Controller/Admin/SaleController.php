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
 
namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CommonBundle\Entity\General\Bank\BankDevice\Amount as BankDeviceAmount,
	CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Entity\General\Bank\MoneyUnit\Amount as MoneyUnitAmount,
	CudiBundle\Entity\Sales\Session,
	CudiBundle\Form\Admin\Sale\CashRegisterAdd as CashRegisterAddForm,
	CudiBundle\Form\Admin\Sale\CashRegisterEdit as CashRegisterEditForm,
	CudiBundle\Form\Admin\Sale\SessionComment as SessionCommentForm,
	Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder;

/**
 * SaleController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sales\Session',
            $this->getParam('page'),
            array(),
			array('openDate' => 'DESC')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function addAction()
    {
        $form = new CashRegisterAddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
                $cashRegister = new CashRegister();
                $this->getEntityManager()->persist($cashRegister);

				$devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();
				foreach($devices as $device) {
					$amountDevice = new BankDeviceAmount($cashRegister, $device, $formData['device_'.$device->getId()]);
					$this->getEntityManager()->persist($amountDevice);
				}
				
				$units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();
				foreach($units as $unit) {
					$amountUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
					$this->getEntityManager()->persist($amountUnit);
				}

                $session = new Session($cashRegister, $this->getAuthentication()->getPersonObject());
                $this->getEntityManager()->persist($session);
				
				$this->getEntityManager()->flush();
				
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The session was successfully added!'
                    )
                );
				
				$this->redirect()->toRoute(
					'admin_sale',
					array(
						'action' => 'edit',
						'id' => $session->getId(),
					)
				);
            }
        }
        
        return array(
        	'form' => $form,
        );
    }
    
    public function editAction()
    {
        $session = $this->_getSession();
		
		$form = new SessionCommentForm($session);

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if($form->isValid($formData)) {
				$session->setComment($formData['comment']);
				
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
				    new FlashMessage(
				        FlashMessage::SUCCESS,
				        'SUCCESS',
                        'The comment was successfully updated!'
				    )
				);
                
                $this->redirect()->toRoute(
                	'admin_sale',
                	array(
                		'action' => 'edit',
                		'id' => $session->getId(),
                	)
                );
			}
		}
		
		return array(
			'session' => $session,
			'units'   => $this->getEntityManager()
			    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
			    ->findAll(),
			'devices' => $this->getEntityManager()
				->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
				->findAll(),
			'form' => $form,
		);
    }

    public function editregisterAction()
    {
        $cashRegister = $this->_getCashRegister();
        
        $session = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Sales\Session')
        	->findOneByCashRegister($cashRegister);

        $form = new CashRegisterEditForm($this->getEntityManager(), $cashRegister);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();

				foreach($devices as $device) {
					$cashRegister->getAmountForDevice($device)
                        ->setAmount($formData['device_'.$device->getId()]);
                }

                $units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();

				foreach($units as $unit) {
					$cashRegister->getAmountForUnit($unit)
                        ->setAmount($formData['unit_'.$unit->getId()]);
                }
                
                $this->getEntityManager()->flush();
				
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The cash register was successfully updated!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'admin_sale',
                	array(
                		'action' => 'edit',
                		'id' => $session->getId(),
                	)
                );
            }
        }
        
        return array(
        	'form' => $form,
        );
    }

    public function closeAction()
    {
        $session = $this->_getSession();
                
        $form = new CashRegisterEditForm($this->getEntityManager(), $session->getOpenAmount());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if($form->isValid($formData)) {
				$cashRegister = new CashRegister();
				
				$devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();
				foreach($devices as $device) {
					$amountDevice = new BankDeviceAmount($cashRegister, $device, $formData['device_'.$device->getId()]);
					$this->getEntityManager()->persist($amountDevice);
				}
				
				$units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();
				foreach($units as $unit) {
					$amountUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
					$this->getEntityManager()->persist($amountUnit);
				}
				
				$session->setCloseAmount($cashRegister)
					->setCloseDate(new \DateTime());
				
				$this->getEntityManager()->persist($cashRegister);
				$this->getEntityManager()->flush();
				
                $this->flashMessenger()->addMessage(
					new FlashMessage(
						FlashMessage::SUCCESS,
						"SUCCESS",
						"The session was successfully closed!"
					)
				);
				
               	$this->redirect()->toRoute(
               		'admin_sale',
               		array(
               			'action' => 'edit',
               			'id' => $session->getId(),
               		)
               	);
			}
		}
		
		return array(
			'form' => $form,
		);
    }
    
    private function _getSession()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the session!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sale',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $session) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No session with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sale',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $session;
    }
    
    private function _getCashRegister()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the cash register!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sale',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $cashRegister = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\CashRegister')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $cashRegister) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No cash register with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sale',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $cashRegister;
    }
}
