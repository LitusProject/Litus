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
	CommonBundle\Entity\General\Bank\BankDeviceAmount,
	CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Entity\General\Bank\MoneyUnitAmount,
	CudiBundle\Entity\Sales\Session,
	CudiBundle\Form\Admin\Sale\CashRegisterAdd as CashRegisterAddForm,
	CudiBundle\Form\Admin\Sale\CashRegisterEdit as CashRegisterEditForm,
	Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder;

/**
 * This class controls management and adding of sale sessions
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CommonBundle\Component\Controller\ActionController
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }
    
    public function manageAction()
    {
		$this->view->sessions = $this->_createPaginator(
            'CudiBundle\Entity\Sales\Session',
			array(),
			array('openDate' => 'DESC')
        );
    }

    public function editregisterAction()
    {
        $register = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\CashRegister')
                ->findOneById($this->_getParam("id"));

        $form = new CashRegisterEditForm();
		$form->populate($register);
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$devices = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                    ->findAll();

				foreach($devices as $device) {
					$register->getAmountForDevice($device)
                        ->setAmount($formData['device_'.$device->getId()]);
                }

                $units = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                    ->findAll();

				foreach($units as $unit) {
					$register->getAmountForUnit($unit)
                        ->setAmount($formData['unit_'.$unit->getId()]);
                }
				
                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The cash register was successfully updated!'
                    )
                );
               	$this->_redirect('managesession', null, null, array('id' => $this->_getParam("session")));
            }
        }
    }
    
    public function managesessionAction()
    {
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($this->_getParam("id"));

        if( !isset($session) )
        	$this->_forward('manage');
		
        $this->view->session = $session;
		$this->view->units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();
		$this->view->devices = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();
		
		$form = new Form\Sale\SessionComment();
		$form->populate($session);
		$this->view->commentForm = $form;

		if($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if($form->isValid($formData)) {
				$session->setComment($formData['comment']);
				
				$this->_addDirectFlashMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The comment was successfully updated!'
                    )
                );
			}
		}
    }

    public function closeAction()
    {
        $session = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Session')
                ->findOneById($this->_getParam('id'));

        if(!isset($session))
            $this->_forward('manage');
        
        $form = new CashRegisterEditForm();
		$form->populate($session->getOpenAmount());
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
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
				
                $this->broker('flashmessenger')->addMessage(
					new FlashMessage(
						FlashMessage::SUCCESS,
						"SUCCESS",
						"The session was successfully closed!"
					)
				);
               	$this->_redirect('managesession', null, null, array('id' => $session->getId()));
			}
		}
    }

    public function newAction()
    {
        $form = new CashRegisterAddForm();
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

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

                $saleSession = new Session($cashRegister, $this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->persist($cashRegister);
                $this->getEntityManager()->persist($saleSession);

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The session was successfully added!'
                    )
                );

               	$this->_redirect('managesession', null, null, array('id' => $saleSession->getId()));
            }
        }
    }
}
