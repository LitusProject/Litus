<?php

namespace Admin;

use Doctrine\ORM\EntityManager;

use \Admin\Form\Sale;

use \Litus\Entity\Cudi\Articles\Sales\SaleSession;

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
    
    public function manageAction() {
        
        $em = $this->getEntityManager();
        $this->view->sessions = $em->getRepository('Litus\Entity\Cudi\Sales\SaleSession')->findAll();
        
    }

    public function newAction() {

        $form = new Form\Sale\CashRegister( array(  '500'=>0, '200'=>0, '100'=>0,
                                                    '50'=>0, '20'=>0, '10'=>0,
                                                    '5'=>0, '2'=>0, '1'=>0,
                                                    '0.5'=>0, '0.2'=>0, '0.1'=>0,
                                                    '0.05'=>0, '0.02'=>0, '0.01'=>0,
                                                    "Bank Device 1"=>'0.0',
                                                    "Bank Device 2"=>'0.0') );
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {

                $em = $this->getEntityManager();

                // create object from formData
                $saleSession = new SaleSession();
         //       $saleSession->manager = $em->getRepository('Litus\Entity\Users\Person')->getById($formData['manager']);
                $saleSession->openDate = date('Y-m-d H:i:s'); // now
                $saleSession->closeDate = null;

                $am = 0;
                $am += $formData['500'] * 50000;
                $am += $formData['200'] * 20000;
                $am += $formData['100'] * 10000;
                $am += $formData['50'] * 5000;
                $am += $formData['20'] * 2000;
                $am += $formData['10'] * 1000;
                $am += $formData['5'] * 500;
                $am += $formData['2'] * 200;
                $am += $formData['1'] * 100;
                $am += $formData['0.5'] * 50;
                $am += $formData['0.2'] * 20;
                $am += $formData['0.1'] * 10;
                $am += $formData['0.05'] * 5;
                $am += $formData['0.02'] * 2;
                $am += $formData['0.01'] * 1;
                $am += $formData['Bank Device 1'] * 100;
                $am += $formData['Bank Device 2'] * 100;

                $saleSession->openAmount = am;


                // persist object into database
                $em->persis( $saleSession );

                // bounce to sale interface
		$this->_forward('manage');

            }

        }

    }
}
