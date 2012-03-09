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
 
namespace CudiBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CudiBundle\Form\Sale\Sale\ReturnBooking;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
    	return array(
    		'socketUrl' => $this->getSocketUrl(),
    	);
    }
    
    public function returnAction()
    {
    	$form = new ReturnBooking($this->getEntityManager());
    	
    	if($this->getRequest()->isPost()) {
    	    $formData = $this->getRequest()->post()->toArray();
    		
    		if ($form->isValid($formData)) {
    			$person = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Users\Person')
    				->findOneByUsername($formData['username']);
    				
    			$article = $this->getEntityManager()
    				->getRepository('CudiBundle\Entity\Stock\StockItem')
    				->findOneByBarcode($formData['article']);
    		
    			$booking = $this->getEntityManager()
    				->getRepository('CudiBundle\Entity\Sales\Booking')
    				->findOneSoldByPersonAndArticle($person, $article);
    			
    			if ($booking) {
    				if ($booking->getNumber() == 1) {
    					$this->getEntityManager()->remove($booking);
    				} else {
    					$booking->setNumber($booking->getNumber() - 1);
    				}
    				
    				$item = $this->getEntityManager()
    					->getRepository('CudiBundle\Entity\Stock\StockItem')
    					->findOneByArticle($article);
    				
    				$item->setNumberInStock($item->getNumberInStock() + 1);
    				
    				$this->getEntityManager()->flush();
    				
    				$this->flashMessenger()->addMessage(
    				    new FlashMessage(
    				        FlashMessage::SUCCESS,
    				        'SUCCESS',
    				        'The booking was successfully returned!'
    				    )
    				);
    			} else {
    				$this->flashMessenger()->addMessage(
    				    new FlashMessage(
    				        FlashMessage::ERROR,
    				        'ERROR',
    				        'The booking could not be returned!'
    				    )
    				);
    			}
    			
    			$this->redirect()->toRoute(
    				'sale_sale',
    				array(
    					'action' => 'return',
    				)
    			);
    		}
    	}
    	
    	return array(
    		'form' => $form,
    	);
    }
}