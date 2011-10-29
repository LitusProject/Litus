<?php

namespace Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use \Admin\Form\Sale;

use \Litus\Entity\Cudi\Sales\SaleSession;
use \Litus\Entity\Cudi\Sales\CashRegister;

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
    
    // default
    public function manageAction()
    {
        $q = $this->getEntityManager()
		  ->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
                  ->createQueryBuilder( "ss" )
                  ->orderBy('ss.openDate', 'DESC')
                  ->setFirstResult(0)    // offset
                  ->setMaxResults(25);   // limit

        $r = $q->getQuery()->getResult();
        $this->view->sessions = $r; // this, like, you know, totally works!!!
    }

    public function editregisterAction()
    {
        $register = $this->getEntityManager()
				->getRepository('Litus\Entity\Cudi\Sales\CashRegister')
				->find($this->_getParam("register_id"));

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
        if( isset( $register ) && !is_null( $register ) && is_object( $register ) )
            $amounts_array = $register->getAmountsArray();

        $session_id = $this->_getParam('session_id');
        if( isset( $session_id ) && !is_null( $session_id ) ) {
            $sesstr = "&session_id=$session_id";
        } else {
            $sesstr = "";
        }
        $form = new Form\Sale\CashRegister();
        $form = $form->setAction("/admin/sale/edit_register?register_id=".$register->getId().$sesstr)
                     ->setMethod('post');

	$form->populate( $amounts_array );
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData) && !isset( $formData['comment'] ) ) {

                $register->setAmountsArray( $formData );

                $this->getEntityManager()->persist( $register );

                if( isset( $session_id ) && !is_null( $session_id ) ) {
                    $this->_forward('manage_session');
                }
                else
                    $this->_forward('manage');
            }
        }
    }
    
    public function managesessionAction()
    {
        $session = $this->getEntityManager()
				->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
				->find($this->_getParam("session_id"));

        if( !isset($session) || is_null($session) ) {
                    $this->_forward('manage');
        }

        else {

    	    $this->view->session = $session;

            $form = new Form\Sale\SessionComment();
            $form = $form->setAction("/admin/sale/manage_session?session_id=".$session->getId())
                         ->setMethod('post');
            $form->populate( array( 'comment' => $session->getComment() ) );
            $this->view->commentForm = $form;

            if($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if(isset($formData['comment']) && $form->isValid($formData)) {

                    $session->setComment( $formData['comment'] );

                    $this->getEntityManager()->persist( $session );
                }
            }
        }
    }

    public function closeAction()
    {
        $form = new Form\Sale\CashRegister();
        $session = $this->getEntityManager()
				->getRepository('Litus\Entity\Cudi\Sales\SaleSession')
				->find($this->_getParam("session_id"));

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

		$this->_forward('manage');
            }
        }
    }

    public function newAction()
    {
        $form = new Form\Sale\CashRegister();
		$form->populate( array(  '500p'=>0, '200p'=>0, '100p'=>0,
                                                    '50p'=>0, '20p'=>0, '10p'=>0,
                                                    '5p'=>0, '2p'=>0, '1p'=>0,
                                                    '0p5'=>0, '0p2'=>0, '0p1'=>0,
                                                    '0p05'=>0, '0p02'=>0, '0p01'=>0,
                                                    'Bank_Device_1'=>'0.0',
                                                    'Bank_Device_2'=>'0.0'));
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $saleSession = new SaleSession();

                $saleSession->setOpenDate( date_create( date('Y-m-d H:i:s', time() ) ) ); // now
                $saleSession->setCloseDate( date_create( date('Y-m-d H:i:s', 0 ) ) ); // 1 jan 1970

            	//$saleSession->setCloseAmount( null );

                $am = new CashRegister( $formData );
                $saleSession->setOpenAmount( $am );

                $this->getEntityManager()->persist( $am );
                $this->getEntityManager()->persist( $saleSession );

		$this->_forward('manage');
            }
        }
    }
}
