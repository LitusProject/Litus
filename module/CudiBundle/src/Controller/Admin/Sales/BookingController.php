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
    CudiBundle\Entity\Sales\Booking,
    CudiBundle\Form\Admin\Sales\Booking\Add as AddForm,
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
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($booking = $this->_getBooking()))
    	    return;
		
		$this->getEntityManager()->remove($booking);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    public function assignAction()
    {
        $this->initAjax();
        
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
    	
    	$email = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.booking_assigned_mail');
			
		$subject = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.booking_assigned_mail_subject');
			
		$mailaddress = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.mail');
			
		$mailname = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.mail_name');
		
		$bookings = '* ' . $booking->getArticle()->getMainArticle()->getTitle() . "\r\n";
	
		$mail = new Message();
		$mail->setBody(str_replace('{{ bookings }}', $bookings, $email))
			->setFrom($mailaddress, $mailname)
			->addTo($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName())
			->setSubject($subject);
		
		// TODO: activate this	
		//$this->getMailTransport()->send($mail);

        $this->getEntityManager()->flush();
            
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully assigned!'
            )
        );
        
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    public function unassignAction()
	{
	    $this->initAjax();
	    
		if (!($booking = $this->_getBooking()))
		    return;

        $booking->setStatus('booked');
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
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