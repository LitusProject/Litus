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
 
namespace CudiBundle\Controller\Admin\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Articles\History,
    CudiBundle\Form\Admin\Prof\Article\Confirm as ArticleForm,
    CudiBundle\Form\Admin\Prof\File\Confirm as FileForm,
    Zend\View\Model\ViewModel;

/**
 * ActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('CudiBundle\Entity\Prof\Action')
        	    ->findAllUncompleted(),
            $this->getParam('page')
        );
        return new ViewModel(
            array(
                'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function completedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('CudiBundle\Entity\Prof\Action')
        	    ->findAllCompleted(),
            $this->getParam('page')
        );
        return new ViewModel(
            array(
                'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function refusedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('CudiBundle\Entity\Prof\Action')
        	    ->findAllRefused(),
            $this->getParam('page')
        );
        return new ViewModel(
            array(
                'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function viewAction()
    {
        if (!($action = $this->_getAction()))
            return new ViewModel();
        
        $action->setEntityManager($this->getEntityManager());
        
        return new ViewModel(
            array(
                'action' => $action,
            )
        );
    }
    
    public function refuseAction()
    {
        if (!($action = $this->_getAction()))
            return new ViewModel();
        
        $action->setRefused($this->getAuthentication()->getPersonObject());
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully refused!'
            )
        );
        
        
        $this->redirect()->toRoute(
        	'admin_prof_action',
        	array(
        		'action' => 'refused'
        	)
        );
    }
    
    public function confirmAction()
    {
        if (!($action = $this->_getAction()))
            return new ViewModel();
        
        $action->setEntityManager($this->getEntityManager());
        
        if ($action->getEntityName() == 'article') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                	'admin_prof_action',
                	array(
                		'action' => 'confirmArticle',
                		'id' => $action->getId(),
                	)
                );
                return new ViewModel();
            } else {
                $edited = $action->getEntity();
                $current = $action->getPreviousEntity();
                $duplicate = $current->duplicate();
                
                $current->setTitle($edited->getTitle())
                    ->setAuthors($edited->getAuthors())
                    ->setPublishers($edited->getPublishers())
                    ->setYearPublished($edited->getYearPublished())
                    ->setISBN($edited->getISBN())
                    ->setURL($edited->getURL())
                    ->setIsDownloadable($edited->isDownloadable())
                    ->setType($edited->getType());
                    
                $edited->setTitle($duplicate->getTitle())
                    ->setAuthors($duplicate->getAuthors())
                    ->setPublishers($duplicate->getPublishers())
                    ->setYearPublished($duplicate->getYearPublished())
                    ->setISBN($duplicate->getISBN())
                    ->setURL($duplicate->getURL())
                    ->setIsProf(false);
                
                if ($current->isInternal()) {
                    $current->setBinding($edited->getBinding())
                        ->setIsRectoVerso($edited->isRectoVerso())
                        ->setIsPerforated($edited->isPerforated());
                        
                    $edited->setBinding($duplicate->getBinding())
                        ->setIsRectoVerso($duplicate->isRectoVerso())
                        ->setIsPerforated($duplicate->isPerforated());
            	}
                
                $history = new History($this->getEntityManager(), $current, $edited);
                $this->getEntityManager()->persist($history);
                
                $action->setEntityId($current->getId())
                    ->setPreviousId($edited->getId());
            }
        } elseif ($action->getEntityName() == 'mapping') {
            if ($action->getAction() == 'add') {
                $action->getEntity()->setIsProf(false);
            } else {
                $action->getEntity()->setRemoved();
            }
        } elseif ($action->getEntityName() == 'file') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                	'admin_prof_action',
                	array(
                		'action' => 'confirmFile',
                		'id' => $action->getId(),
                	)
                );
                return new ViewModel();
            } else {
                $action->getEntity()->setRemoved();
            }
        }
        
        $action->setCompleted($this->getAuthentication()->getPersonObject());
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully confirmed!'
            )
        );
        
        $this->redirect()->toRoute(
        	'admin_prof_action',
        	array(
        		'action' => 'completed'
        	)
        );
        
        return new ViewModel();
    }
    
    public function confirmArticleAction()
    {
        if (!($action = $this->_getAction()))
            return new ViewModel();
        
        $action->setEntityManager($this->getEntityManager());
            
        $form = new ArticleForm($this->getEntityManager(), $action->getEntity());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $action->getEntity()->setTitle($formData['title'])
        	        ->setAuthors($formData['author'])
        	        ->setPublishers($formData['publisher'])
        	        ->setYearPublished($formData['year_published'])
        	        ->setISBN($formData['isbn'] != ''? $formData['isbn'] : null)
        	        ->setURL($formData['url'])
        	        ->setIsDownloadable($formData['downloadable'])
        	        ->setType($formData['type']);
        	    
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Binding')
						->findOneById($formData['binding']);

					$frontPageColor = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Color')
						->findOneById($formData['front_color']);
                    
                    $action->getEntity()->setNbBlackAndWhite($formData['nb_black_and_white'])
                    	->setNbColored($formData['nb_colored'])
                    	->setBinding($binding)
                    	->setIsOfficial($formData['official'])
                    	->setIsRectoVerso($formData['rectoverso'])
                    	->setFrontColor($frontPageColor)
                    	->setIsPerforated($formData['perforated']);
				}
				
				$action->getEntity()->setIsProf(false);
        	    
        	    $action->setCompleted($this->getAuthentication()->getPersonObject());
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_prof_action',
        	    	array(
        	    		'action' => 'completed'
        	    	)
        	    );
        	    return new ViewModel();
        	}
        }
        
        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
    
    public function confirmFileAction()
    {
        if (!($action = $this->_getAction()))
            return new ViewModel();
        
        $action->setEntityManager($this->getEntityManager());
            
        $form = new FileForm($action->getEntity());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $action->getEntity()
        	        ->setPrintable($formData['printable'])
        	        ->getFile()->setDescription($formData['description']);
				
				$action->getEntity()->setIsProf(false);
        	    
        	    $action->setCompleted($this->getAuthentication()->getPersonObject());
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_prof_action',
        	    	array(
        	    		'action' => 'completed'
        	    	)
        	    );
        	    return new ViewModel();
        	}
        }
        
        return new ViewModel(
            array(
                'form' => $form,
            )
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
    			'admin_prof_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $action = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
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
    			'admin_prof_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $action;
    }
}