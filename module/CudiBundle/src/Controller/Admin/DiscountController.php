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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Articles\Discount\Discount,
    CudiBundle\Form\Admin\Discount\Add as DiscountForm;

/**
 * DiscountController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DiscountController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
        $form = new DiscountForm($article, $this->getEntityManager());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
                $type = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Articles\Discount\Type')
                    ->findOneById($formData['type']); 
                
				$discount = new Discount($article, $formData['value'], $formData['method'], $type);
				
				$this->getEntityManager()->persist($discount);
                $this->getEntityManager()->flush();
                
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The comment was successfully created!'
                    )
                );
                
				$this->redirect()->toRoute(
					'admin_discount',
					array(
						'action' => 'manage',
						'id' => $article->getId(),
					)
				);
				
				return;
			}
        }
        
        return array(
        	'article' => $article,
        	'form' => $form,
        	'discounts' => $this->getEntityManager()
        	    ->getRepository('CudiBundle\Entity\Articles\Discount\Discount')
        	    ->findAllByArticle($article),
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($discount = $this->_getDiscount()))
    	    return;
		
		$this->getEntityManager()->remove($discount);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    private function _getArticle()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
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
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
    
    private function _getDiscount()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the discount!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $discount = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\Discount\Discount')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $discount) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No discount with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $discount;
    }
}