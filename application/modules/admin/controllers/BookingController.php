<?php

namespace Admin;

use \Doctrine\ORM\EntityManager;

use \Admin\Form\Booking\Add;

use \Litus\Entity\Cudi\Sales\Booking;

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
        $this->_forward('add');
    }

    public function addAction()
    {
        $form = new Add();
		
        $this->view->form = $form;
		$this->view->bookingCreated = false;

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
				$this->view->bookingCreated = true;
            }
        }
    }
    
    public function manageAction()
	{
        $em = $this->getEntityManager();
        $this->view->bookings = $em->getRepository('Litus\Entity\Cudi\Sales\Booking')->findAll();        
    }
}