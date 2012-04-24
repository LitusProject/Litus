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
 
namespace CudiBundle\Controller\Admin;

use CudiBundle\Entity\Sales\Booking,
	CudiBundle\Form\Admin\Booking\Add as AddForm,
	CommonBundle\Component\FlashMessenger\FlashMessage,
	Doctrine\ORM\EntityManager,
	Zend\Json\Json,
	Zend\Mail\Message,
	Zend\Mail\Transport\Sendmail;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CommonBundle\Component\Controller\ActionController
{

	public function manageAction()
	{
	    $paginator = $this->paginator()->createFromArray(
	        $this->getEntityManager()
	            ->getRepository('CudiBundle\Entity\Sales\Booking')
	            ->findAllActive(),
	        $this->getParam('page')
	    );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function inactiveAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllInactive(),
            $this->getParam('page')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function assignAction()
    {
        $this->initAjax();
        
        if (!($booking = $this->_getBooking()))
            return;
            
        $stockItem = $booking->getArticle()->getStockItem();
        $stockItem->setEntityManager($this->getEntityManager());
        
        if ($stockItem->getNumberAvailable() <= 0 || $stockItem->getNumberAvailable() < $booking->getNumber()) {
    		return array(
    		    'result' => (object) array("status" => "error")
    		);
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
		
		$transport = new Sendmail();

		$bookings = '* ' . $booking->getArticle()->getTitle() . "\r\n";
	
		$mail = new Message();
		$mail->setBody(str_replace('{{ bookings }}', $bookings, $email))
			->setFrom($mailaddress, $mailname)
			->addTo($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName())
			->setSubject($subject);
			
		$transport->send($mail);

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
    
    public function addAction()
    {
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
						->getRepository('CudiBundle\Entity\Stock\StockItem')
						->findOneByBarcode($formData['stockArticle']),
					'booked',
					$formData['number']);
                 
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
					'admin_booking',
					array(
						'action' => 'manage'
					)
				);
				
				return;
			}
        }
        
        return array(
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
	
	public function searchAction()
	{
		$this->initAjax();
		
		$active = $this->getParam('type') == 'active';
		
		switch($this->getParam('field')) {
			case 'person':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByPersonName($this->getParam('string'), $active);
				break;
			case 'article':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByArticle($this->getParam('string'), $active);
				break;
			case 'status':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByStatus($this->getParam('string'), $active);
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
			$item->article = $booking->getArticle()->getTitle();
			$item->number = $booking->getNumber();
			$item->bookDate = $booking->getBookDate()->format('d/m/Y H:i');
			$item->status = $booking->getStatus();
			$item->versionNumber = $booking->getArticle()->getVersionNumber();
			$result[] = $item;
		}
		
		return array(
			'result' => $result,
		);
	}
	
	public function assignAllAction()
	{
		$number = $this->getEntityManager()
			->getRepository('CudiBundle\Entity\Stock\StockItem')
			->assignAll();
		
		$this->getEntityManager()->flush();
		
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

		$this->redirect()->toRoute(
			'admin_booking',
			array(
				'action' => 'manage'
			)
		);
		
		return;
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
				'admin_booking',
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
				'admin_booking',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $article;
	}
}