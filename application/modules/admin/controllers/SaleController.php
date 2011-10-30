<?php

namespace Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use \Admin\Form\Sale;

use \Litus\Entity\Cudi\Sales\SaleSession;
use \Litus\Entity\Cudi\Sales\CashRegister;
use \Litus\Entity\Cudi\Sales\NumberMoneyUnit;

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
        $this->view->sessions = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\SaleSession')->findFirstNb(25);
    }

    public function editregisterAction()
    {
        $register = $this->getEntityManager()
                ->getRepository('Litus\Entity\Cudi\Sales\CashRegister')
                ->findOneById($this->_getParam("id"));

        $form = new Form\Sale\CashRegister();
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
		$this->view->units = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
		
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
        $session_id = $this->_getParam("session_id");

        if( !isset( $session_id ) || is_null( $session_id ) ) {
            $this->_forward( 'manage' );
        } else {

            $form = new Form\Sale\CashRegister();
            $session = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
                    ->find($session_id);

            // default: all zeros
            $amounts_array = array(  '500p'=>0, '200p'=>0, '100p'=>0,
                                 '50p'=>0, '20p'=>0, '10p'=>0,
                                 '5p'=>0, '2p'=>0, '1p'=>0,
                                 '0p5'=>0, '0p2'=>0, '0p1'=>0,
                                 '0p05'=>0, '0p02'=>0, '0p01'=>0,
                                 'Bank_Device_1'=>'0.0',
                                 'Bank_Device_2'=>'0.0');

            // but if we can find the amounts in de cash register at the start of the sale session,
            // use those amounts instead
            if( isset( $session ) && !is_null( $session ) && is_object( $session ) )
                $amounts_array = $session->getOpenAmount()->getAmountsArray();

            $form->populate( $amounts_array );
            $this->view->form = $form;

            if($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if($form->isValid($formData)) {
                    $session->setCloseDate( date_create( date('Y-m-d H:i:s', time() ) ) ); // now

                    $am = new CashRegister( $formData );
                    $session->setCloseAmount( $am );

                    $this->getEntityManager()->persist( $am );
                    $this->getEntityManager()->persist( $session );

                    $this->_forward('manage_session');
                }
            }
        }
    }

    public function newAction()
    {
        $form = new Form\Sale\CashRegister();
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $cashRegister = new CashRegister($formData['Bank_Device_1'], $formData['Bank_Device_2']);
				$units = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
				foreach($units as $unit) {
					$numberUnit = new NumberMoneyUnit($cashRegister, $unit, $formData['unit_'.$unit->getId()]);
					$this->getEntityManager()->persist($numberUnit);
				}

                $saleSession = new SaleSession($cashRegister, "");

                $this->getEntityManager()->persist($cashRegister);
                $this->getEntityManager()->persist($saleSession);

        		$this->_redirect('manage');
            }
        }
    }
}
