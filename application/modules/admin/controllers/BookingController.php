<?php

namespace Admin;

use \Doctrine\ORM\EntityManager;

use \Admin\Form\Booking\Add;

use \Litus\Entity\Cudi\Sales\Booking;
use \Litus\FlashMessenger\FlashMessage;

/**
 *
 * This class controlls management and adding of bookings.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 */
class BookingController extends \Litus\Controller\Action
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
		$this->view->paginator = $this->_createPaginator(
            'Litus\Entity\Cudi\Sales\Booking'
        );
    }

    public function addAction()
    {
        $form = new Add();
		
        $this->view->form = $form;

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $person = $this->getEntityManager()
					->getRepository('Litus\Entity\Users\Person')
					->findOneByUsername($formData['person']);
                $article = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findOneByBarcode($formData['stockArticle']);
				
				$booking = new Booking($person, $article, 'booked', $formData['number']);
                 
                $this->getEntityManager()->persist($booking);
				$this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The booking was successfully created!'
                    )
                );
				$this->_redirect('manage');
			}
        }
    }

	public function deleteAction()
	{
		$booking = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Sales\Booking')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $booking)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$this->view->booking = $booking;
		
		if (null !== $this->getRequest()->getParam('confirm')) {
			if (1 == $this->getRequest()->getParam('confirm')) {
				$this->getEntityManager()->remove($booking);

				$this->broker('flashmessenger')->addMessage(
            		new FlashMessage(
                		FlashMessage::SUCCESS,
                    	'SUCCESS',
                    	'The booking was successfully removed!'
                	)
            	);
			};
            
			$this->_redirect('manage', null, null, array('id' => null));
        }
	}
	
	public function assignAction()
	{
		$number = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Stock\StockItem')->assignAll();
		
		if (0 == $number)
			$message = 'No booking could be assigned!';
		elseif (1 == $number)
			$message = 'There is <b>one</b> booking assigned!';
		else
			$message = 'There are <b>' . $number . '</b> bookings assigned!';
		
		$this->broker('flashmessenger')->addMessage(
    		new FlashMessage(
        		FlashMessage::SUCCESS,
            	'SUCCESS',
            	$message
        	)
    	);

		$this->_redirect('manage');
	}
}