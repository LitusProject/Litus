<?php

namespace Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use \Admin\Form\Sale\CashRegister as CashRegisterForm;

use \Litus\Entity\Cudi\Sales\SaleSession;
use \Litus\Entity\Cudi\Sales\CashRegister;
use \Litus\Entity\Cudi\Sales\MoneyUnitAmount;
use \Litus\FlashMessenger\FlashMessage;


/**
 *
 * This class controlls management and adding of sale sessions
 * @author Alan
 *
 */
class SaleController extends \Litus\Controller\Action
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
		// TODO: order
		$this->view->sessions = $this->_createPaginator(
            'Litus\Entity\Cudi\Sales\SaleSession'
        );
    }

    public function editregisterAction()
    {
        $register = $this->getEntityManager()
                ->getRepository('Litus\Entity\Cudi\Sales\CashRegister')
                ->findOneById($this->_getParam("id"));

        $form = new CashRegisterForm();
		$form->populate($register);
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$register->setAmountBank1($formData['Bank_Device_1']);
				$register->setAmountBank2($formData['Bank_Device_2']);
				$units = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
				foreach($units as $unit)
					$register->getNumberForUnit($unit)->setNumber($formData['unit_'.$unit->getId()]);
				
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
            ->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
            ->findOneById($this->_getParam("id"));

        if( !isset($session) )
        	$this->_forward('manage');
		
        $this->view->session = $session;
		$this->view->units = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')
            ->findAll();
		
		$form = new Form\Sale\SessionComment();
		$form->populate($session);
		$this->view->commentForm = $form;

		if($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if($form->isValid($formData)) {
				$session->setComment( $formData['comment'] );
			}
		}
    }

    public function closeAction()
    {
        $session = $this->getEntityManager()
                ->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
                ->findOneById($this->_getParam('id'));

        if(!isset($session))
            $this->_forward('manage');
        
        $form = new CashRegisterForm();
		$form->populate($session->getOpenAmount());
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if($form->isValid($formData)) {
				$cashRegister = new CashRegister($formData['Bank_Device_1'], $formData['Bank_Device_2']);
				$units = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
				foreach($units as $unit) {
					$numberUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
					$this->getEntityManager()->persist($numberUnit);
				}
				
				$session->setCloseAmount($cashRegister)
					->setCloseDate(new \DateTime());
				
				$this->getEntityManager()->persist($cashRegister);
				
                $this->broker('flashmessenger')->addMessage(new FlashMessage(FlashMessage::SUCCESS, "SUCCESS", "The session was successfully closed!"));
               	$this->_redirect('managesession', null, null, array('id' => $session->getId()));
			}
		}
    }

    public function newAction()
    {
        $form = new CashRegisterForm();
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $cashRegister = new CashRegister($formData['Bank_Device_1'], $formData['Bank_Device_2']);
				$units = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
				foreach($units as $unit) {
					$numberUnit = new MoneyUnitAmount($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
					$this->getEntityManager()->persist($numberUnit);
				}

                $saleSession = new SaleSession($cashRegister, "");

                $this->getEntityManager()->persist($cashRegister);
                $this->getEntityManager()->persist($saleSession);

                $this->broker('flashmessenger')->addMessage(new FlashMessage(FlashMessage::SUCCESS, "SUCCESS", "The session was successfully added!"));
        		$this->_redirect('manage');
            }
        }
    }
}