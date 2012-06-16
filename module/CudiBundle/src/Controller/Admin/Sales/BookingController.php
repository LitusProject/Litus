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
 
namespace CudiBundle\Controller\Admin\Sales;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Sales\Booking,
    CudiBundle\Form\Admin\Sales\Booking\Add as AddForm,
    DateInterval,
	Zend\Mail\Message;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($currentPeriod = $this->_getActiveStockPeriod()))
            return;
        
        if (!($activePeriod = $this->_getPeriod()))
            return;
                
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllActiveByPeriod($activePeriod),
            $this->getParam('page')
        );
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
                    
        return array(
            'periods' => $periods,
            'activePeriod' => $activePeriod,
            'currentPeriod' => $currentPeriod,
            'paginator' => $paginator,
            'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function inactiveAction()
    {
        if (!($currentPeriod = $this->_getActiveStockPeriod()))
            return;
        
        if (!($activePeriod = $this->_getPeriod()))
            return;
                
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllInactiveByPeriod($activePeriod),
            $this->getParam('page')
        );
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
                    
        return array(
            'periods' => $periods,
            'activePeriod' => $activePeriod,
            'currentPeriod' => $currentPeriod,
            'paginator' => $paginator,
            'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function addAction()
    {
        if (!($currentPeriod = $this->_getActiveStockPeriod()))
            return;
        
        if (!($activePeriod = $this->_getPeriod()))
            return;
            
        $form = new AddForm($this->getEntityManager());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$booking = new Booking(
					$this->getEntityManager(),
					$this->getEntityManager()
						->getRepository('CommonBundle\Entity\Users\Person')
						->findOneByUsername($formData['person']),
					$this->getEntityManager()
						->getRepository('CudiBundle\Entity\Sales\Article')
						->findOneByBarcode($formData['article']),
					'booked',
					$formData['number']
				);
                 
                $this->getEntityManager()->persist($booking);
                $this->getEntityManager()->flush();
                
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The booking was successfully created!'
                    )
                );
                
				$this->redirect()->toRoute(
					'admin_sales_booking',
					array(
						'action' => 'manage'
					)
				);
				
				return;
			}
        }
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
             
        return array(
            'periods' => $periods,
            'activePeriod' => $activePeriod,
            'currentPeriod' => $currentPeriod,
            'form' => $form,
        );
    }
    
    public function editAction()
    {
        if (!($booking = $this->_getBooking()))
            return;
            
        if (!($currentPeriod = $this->_getActiveStockPeriod()))
            return;
        
        if (!($activePeriod = $this->_getPeriod()))
            return;
            
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
            
        return array(
            'periods' => $periods,
            'activePeriod' => $activePeriod,
            'currentPeriod' => $currentPeriod,
            'booking' => $booking,
        );
    }
    
    public function deleteAction()
    {
        if (!($booking = $this->_getBooking()))
    	    return;
		
		$this->getEntityManager()->remove($booking);
		$this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully removed!'
            )
        );
        
        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
    }
    
    public function assignAction()
    {
        if (!($booking = $this->_getBooking()))
            return;
            
        if (!($currentPeriod = $this->_getActiveStockPeriod()))
            return;
        
        $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
        if ($available <= 0) {
    		return array(
    		    'result' => (object) array("status" => "error")
    		);
    	}
    	
    	if ($available < $booking->getNumber()) {
    	    $new = new Booking(
    	    	$this->getEntityManager(),
    	    	$booking->getPerson(),
    	    	$booking->getArticle(),
    	    	'booked',
    	    	$booking->getNumber() - $available
    	    );
    	    
    	    $booking->setNumber($available);
    	}
    	
    	$booking->setStatus('assigned');
    	
    	$message = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.booking_assigned_mail');
			
		$subject = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.booking_assigned_mail_subject');
			
		$mailAddress = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.mail');
			
		$mailName = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.mail_name');
		
        BookingMail::sendMail($this->getMailTransport(), array($booking), $booking->getPerson(), $message, $subject, $mailAddress, $mailName);

        $this->getEntityManager()->flush();
            
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully assigned!'
            )
        );
        
        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
    }
    
    public function unassignAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return;

        $booking->setStatus('booked');
		$this->getEntityManager()->flush();
		    
		$this->flashMessenger()->addMessage(
		    new FlashMessage(
		        FlashMessage::SUCCESS,
		        'SUCCESS',
		        'The booking was successfully unassigned!'
		    )
		);
		
		$this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
	}
	
	public function expireAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return;

        $booking->setStatus('expired');
		$this->getEntityManager()->flush();
		    
		$this->flashMessenger()->addMessage(
		    new FlashMessage(
		        FlashMessage::SUCCESS,
		        'SUCCESS',
		        'The booking was successfully expired!'
		    )
		);
		
		$this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
	}
	
	public function extendAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return;
        
        $extendTime = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.reservation_extend_time');
        
        $date = clone $booking->getExpirationDate();
		$booking->setExpirationDate($date->add(new DateInterval($extendTime)));
		$this->getEntityManager()->flush();
		
		$this->flashMessenger()->addMessage(
		    new FlashMessage(
		        FlashMessage::SUCCESS,
		        'SUCCESS',
		        'The booking was successfully extended!'
		    )
		);
		
		$this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
	}
	
	public function assignAllAction()
	{
	    $number = $this->getEntityManager()
			->getRepository('CudiBundle\Entity\Sales\Booking')
			->assignAll($this->getMailTransport());
				
		if (0 == $number)
			$message = 'No booking could be assigned!';
		elseif (1 == $number)
			$message = 'There is <b>one</b> booking assigned!';
		else
			$message = 'There are <b>' . $number . '</b> bookings assigned!';
		
		$this->flashMessenger()->addMessage(
    		new FlashMessage(
        		FlashMessage::SUCCESS,
            	'SUCCESS',
            	$message
        	)
    	);

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
	}
	
	public function searchAction()
	{
	    $this->initAjax();
	    
	    if (!($activePeriod = $this->_getPeriod()))
	        return;
	    	    
	    switch($this->getParam('field')) {
	    	case 'person':
	    		$bookings = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Booking')
	    			->findAllByPersonNameAndTypeAndPeriod($this->getParam('string'), $this->getParam('type'), $activePeriod);
	    		break;
	    	case 'article':
	    		$bookings = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Booking')
	    			->findAllByArticleAndTypeAndPeriod($this->getParam('string'), $this->getParam('type'), $activePeriod);
	    		break;
	    	case 'status':
	    		$bookings = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Booking')
	    			->findAllByStatusAndTypeAndPeriod($this->getParam('string'), $this->getParam('type'), $activePeriod);
	    		break;
	    }
	    
	    $numResults = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('search_max_results');
	    
	    array_splice($bookings, $numResults);
	    
	    $result = array();
	    foreach($bookings as $booking) {
	    	$item = (object) array();
	    	$item->id = $booking->getId();
	    	$item->person = $booking->getPerson()->getFullName();
	    	$item->article = $booking->getArticle()->getMainArticle()->getTitle();
	    	$item->number = $booking->getNumber();
	    	$item->bookDate = $booking->getBookDate()->format('d/m/Y H:i');
	    	$item->status = ucfirst($booking->getStatus());
	    	$result[] = $item;
	    }
	    
	    return array(
	    	'result' => $result,
	    );
	}
    
    private function _getPeriod()
    {
    	if (null === $this->getParam('period')) {
    		return $this->_getActiveStockPeriod();
    	}
    
        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneById($this->getParam('period'));
    	
    	if (null === $supplier) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No period with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_booking',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $supplier;
    }
    
    private function _getBooking()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the booking!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_booking',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No booking with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_booking',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}