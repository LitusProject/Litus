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
    CudiBundle\Form\Admin\Mail\Send as MailForm,
    CudiBundle\Form\Admin\Sales\Booking\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Booking\Article as ArticleForm,
    CudiBundle\Form\Admin\Sales\Booking\Person as PersonForm,
    DateInterval,
	Zend\Mail\Message,
	Zend\View\Model\ViewModel;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();
        
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();
                
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllActiveByPeriod($activePeriod),
            $this->getParam('page')
        );
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
                    
        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function inactiveAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();
        
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();
                
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllInactiveByPeriod($activePeriod),
            $this->getParam('page')
        );
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
                    
        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function addAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();
        
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();
            
        $academicYear = $this->getAcademicYear();
            
        $form = new AddForm($this->getEntityManager());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$booking = new Booking(
					$this->getEntityManager(),
					$this->getEntityManager()
						->getRepository('CommonBundle\Entity\Users\People\Academic')
						->findOneById($formData['person_id']),
					$this->getEntityManager()
						->getRepository('CudiBundle\Entity\Sales\Article')
						->findOneById($formData['article_id']),
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
				
				return new ViewModel();
			}
        }
        
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
             
        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }
    
    public function editAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();
            
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();
        
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();
            
        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();
                
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllByPersonAndPeriod($booking->getPerson(), $activePeriod),
            $this->getParam('page')
        );
        
        $mailForm = new MailForm($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName());
        $mailForm->setAction($this->url()->fromRoute('admin_cudi_mail'));
            
        return new ViewModel(
            array(
                'mailForm' => $mailForm,
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'booking' => $booking,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }
    
    public function deleteAction()
    {
        if (!($booking = $this->_getBooking()))
    	    return new ViewModel();
		
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
            return new ViewModel();
            
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();
        
        $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
        if ($available <= 0) {
    		return new ViewModel(
    		    array(
    		        'result' => (object) array("status" => "error"),
    		    )
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
        
        return new ViewModel();
    }
    
    public function unassignAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return new ViewModel();

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
		
		return new ViewModel();
	}
	
	public function expireAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return new ViewModel();

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
		
		return new ViewModel();
	}
	
	public function extendAction()
	{
	    if (!($booking = $this->_getBooking()))
		    return new ViewModel();
        
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
		
		return new ViewModel();
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
        
        return new ViewModel();
	}
	
	public function searchAction()
	{
	    $this->initAjax();
	    
	    if (!($activePeriod = $this->_getPeriod()))
	        return new ViewModel();
	    	    
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
	    
	    return new ViewModel(
	        array(
    	    	'result' => $result,
    	    )
	    );
	}
	
	public function personAction()
	{
	    if (!($activePeriod = $this->_getPeriod()))
	        return new ViewModel();
	        
	    $return = new ViewModel();
	     
	    $form = new PersonForm();
        $return->form = $form;

	    if ($person = $this->_getPerson()) {
	        $paginator = $this->paginator()->createFromArray(
	            $this->getEntityManager()
	                ->getRepository('CudiBundle\Entity\Sales\Booking')
	                ->findAllByPersonAndPeriod($person, $activePeriod),
	            $this->getParam('page')
	        );
	        
	        $return->paginator = $paginator;
	        $return->paginationControl = $this->paginator()->createControl();
	        $return->person = $person;
	    }
	    
	    return $return;
	}
	
	public function articleAction()
	{
	    if (!($activePeriod = $this->_getPeriod()))
	        return new ViewModel();
	        
	    $return = new ViewModel();
	     
	    $form = new ArticleForm();
        $return->form = $form;
	    $return->currentAcademicYear = $this->getAcademicYear();;

	    if ($article = $this->_getArticle()) {
	        $paginator = $this->paginator()->createFromArray(
	            $this->getEntityManager()
	                ->getRepository('CudiBundle\Entity\Sales\Booking')
	                ->findAllByArticleAndPeriod($article, $activePeriod),
	            $this->getParam('page')
	        );
	        
	        $return->paginator = $paginator;
	        $return->paginationControl = $this->paginator()->createControl();
	        $return->article = $article;
	    }
	    
	    return $return;
	}
    
    private function _getPeriod()
    {
    	if (null === $this->getParam('period')) {
    		return $this->getActiveStockPeriod();
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
    
    private function _getPerson()
    {
    	if (null === $this->getParam('id')) {
    		return;
    	}
    
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $person) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No person with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_booking',
    			array(
    				'action' => 'person'
    			)
    		);
    		
    		return;
    	}
    	
    	return $person;
    }
    
    private function _getArticle()
    {
    	if (null === $this->getParam('id')) {
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_booking',
    			array(
    				'action' => 'article'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}