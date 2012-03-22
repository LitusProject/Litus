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
	Zend\Json\Json;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CommonBundle\Component\Controller\ActionController
{

	public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sales\Booking',
            $this->getParam('page'),
            array(),
            array('bookDate' => 'DESC')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
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
			}
        }
        
        return array(
        	'form' => $form,
        );
    }
    
    public function deleteAction()
	{
		$this->initAjax();

		$booking = $this->_getBooking();
		
		$this->getEntityManager()->remove($booking);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
	}
	
	public function searchAction()
	{
		$this->initAjax();
		
		switch($this->getParam('field')) {
			case 'person':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByPersonName($this->getParam('string'));
				break;
			case 'article':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByArticle($this->getParam('string'));
				break;
			case 'status':
				$bookings = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Booking')
					->findAllByStatus($this->getParam('string'));
				break;
		}
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
	
	public function assignAction()
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
	}

    public function unassignAction()
	{
		$booking = $this->_getBooking();
			
		if (null !== $this->getParam('confirm')) {
			if (1 == $this->getParam('confirm')) {
				$booking->setStatus('booked');
				
				$this->getEntityManager()->flush();

				$this->flashMessenger()->addMessage(
            		new FlashMessage(
                		FlashMessage::SUCCESS,
                    	'SUCCESS',
                    	'The booking was successfully unassigned!'
                	)
            	);
			};
            
			$this->redirect()->toRoute(
				'admin_booking',
				array(
					'action' => 'manage'
				)
			);
        }
        
        return array(
        	'booking' => $booking,
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