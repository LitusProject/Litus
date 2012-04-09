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
 
namespace ProfBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    ProfBundle\Form\Admin\Article\Confirm as ConfirmForm;

/**
 * ActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllUncompleted(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function completedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllCompleted(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function refusedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllRefused(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function viewAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        if ($action->getEntity() == 'article' && $action->getAction() == 'edit') {
            foreach($action->getItems() as $item)
                $item->setEntityManager($this->getEntityManager());
        }
        
        return array(
            'action' => $action,
        );
    }
    
    public function refuseAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setCompletedPerson($this->getAuthentication()->getPersonObject())
            ->setRefused();
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully refused!'
            )
        );
        
        
        $this->redirect()->toRoute(
        	'admin_action',
        	array(
        		'action' => 'refused'
        	)
        );
    }
    
    public function confirmAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        if ($action->getEntity() == 'article') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                	'admin_action',
                	array(
                		'action' => 'confirmArticle',
                		'id' => $action->getId(),
                	)
                );
                return;
            } else {
                foreach($action->getItems() as $item) {
                    switch ($item->getField()) {
                        case 'title':
                            $action->getArticle()->setTitle($item->getNewValue());
                            break;
                        case 'author':
                            $action->getArticle()->getMetaInfo()->setAuthors($item->getNewValue());
                            break;
                        case 'publisher':
                            $action->getArticle()->getMetaInfo()->setPublishers($item->getNewValue());
                            break;
                        case 'year_published':
                            $action->getArticle()->getMetaInfo()->setYearPublished($item->getNewValue());
                            break;
                        case 'binding':
                            $action->getArticle()->setBinding($this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
                                ->findOneById($item->getValue())
                            );
                            break;
                        case 'rectoverso':
                            $action->getArticle()->setIsRectoVerso($item->getNewValue());
                            break;
                    }
                }
            }
        } elseif ($action->getEntity() == 'mapping') {
            if ($action->getAction() == 'add') {
                $action->getArticleSubjectMap()->setEnabled();
            } else {
                $action->getArticleSubjectMap()->setRemoved();
            }
        } elseif ($action->getEntity() == 'file') {
            if ($action->getAction() == 'add') {
                $action->getFile()->setEnabled();
            } else {
                $action->getFile()->setRemoved();
            }
        }
        
        $action->setCompletedPerson($this->getAuthentication()->getPersonObject())
            ->setCompleted();
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully confirmed!'
            )
        );
        
        $this->redirect()->toRoute(
        	'admin_action',
        	array(
        		'action' => 'completed'
        	)
        );
    }
    
    public function confirmArticleAction()
    {
        if (!($action = $this->_getAction()))
            return;
            
        $form = new ConfirmForm($this->getEntityManager(), $action->getArticle());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $action->getArticle()->getMetaInfo()->setAuthors($formData['author'])
					->setPublishers($formData['publisher'])
					->setYearPublished($formData['year_published']);
				
				$action->getArticle()->setTitle($formData['title']);
				
				if ($formData['stock']) {
					$supplier = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Supplier')
						->findOneById($formData['supplier']);
						
					$action->getArticle()->setIsBookable($formData['bookable'])
						->setIsUnbookable($formData['unbookable'])
						->setSupplier($supplier)
						->setCanExpire($formData['can_expire']);
				}
				
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
						->findOneById($formData['binding']);

					$frontColor = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\StockArticles\Color')
						->findOneById($formData['front_color']);
						
					$action->getArticle()->setNbBlackAndWhite($formData['nb_black_and_white'])
						->setNbColored($formData['nb_colored'])
						->setBinding($binding)
						->setIsOfficial($formData['official'])
						->setIsRectoVerso($formData['rectoverso'])
						->setFrontColor($frontColor)
						->setFrontPageTextColored($formData['front_text_colored']);
				}
				
				$action->getArticle()->setEnabled();
        	    
        	    $action->setCompletedPerson($this->getAuthentication()->getPersonObject())
        	        ->setCompleted();
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_action',
        	    	array(
        	    		'action' => 'completed'
        	    	)
        	    );
        	    return;
        	}
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function _getAction()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the action!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $action = $this->getEntityManager()
            ->getRepository('ProfBundle\Entity\Action')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $action) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No action with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $action;
    }
}