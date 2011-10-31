<?php

namespace Admin;

use \Doctrine\ORM\EntityManager;

use \Admin\Form\Booking\Add;

use \Litus\Entity\Cudi\Sales\Booking;
use \Litus\FlashMessenger\FlashMessage;

/**
 *
 * This class controlls management and adding of bookings.
 * @author Kristof Mariën
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
					->getRepository('Litus\Entity\Cudi\Articles\StockArticles\External')
					->findOneByBarcode($formData['stockArticle']);
				if (null == $article) {
					$article = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Internal')
						->findOneByBarcode($formData['stockArticle']);
				}
				
				$booking = new Booking($person, $article, 'booked');
                 
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
    
    public function manageAction()
	{
		$this->view->paginator = $this->_createPaginator(
            'Litus\Entity\Cudi\Sales\Booking'
        );
    }
}