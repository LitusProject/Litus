<?php

namespace NewsBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    NewsBundle\Entity\Nodes\News,
    NewsBundle\Entity\Nodes\Translation,
    NewsBundle\Form\Admin\News\Add as AddForm,
    NewsBundle\Form\Admin\News\Edit as EditForm;

/**
 * Handles system admin for news.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('NewsBundle\Entity\Nodes\News')
                ->findAll(),
            $this->getParam('page')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(),
        );
    }
    
    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
            if ($form->isValid($formData)) {
                $news = new News(
                	$this->getAuthentication()->getPersonObject()
                );
                $this->getEntityManager()->persist($news);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();
                
                foreach($languages as $language) {
                    $translation = new Translation(
                    	$news,
                    	$language,
                    	$formData['content_' . $language->getAbbrev()],
                    	$formData['title_' . $language->getAbbrev()]
                    );
                    $this->getEntityManager()->persist($translation);
                    
                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The news item was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_news',
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
    
    public function editAction()
    {
        if (!($news = $this->_getNews()))
            return;
        
        $form = new EditForm($this->getEntityManager(), $news);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
            if ($form->isValid($formData)) {
                $news->setUpdatePerson($this->getAuthentication()->getPersonObject());

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();
                
                foreach($languages as $language) {
                    $translation = $news->getTranslation($language);
                    
                    if ($translation) {
                        $translation->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        $translation = new Translation(
                        	$news, $language, $formData['content_' . $language->getAbbrev()], $formData['title_' . $language->getAbbrev()]
                        );
                        $this->getEntityManager()->persist($translation);
                    }
                    
                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The news was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_news',
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
        
        if (!($news = $this->_getNews()))
            return;
        
        $this->getEntityManager()->remove($news);
        
        $this->getEntityManager()->flush();
    	
    	return array(
    		'result' => array(
    			'status' => 'success'
    		),
    	);
    }
    
    public function _getNews()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the news!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_news',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $news) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No news with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_news',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $news;
    }
}