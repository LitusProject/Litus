<?php

namespace Admin;

use Doctrine\ORM\EntityManager;

use \Admin\Form\Article\Add;
use \Admin\Form\Article\Edit;
use \Admin\Form\Article\NewVersion;
use \Admin\Form\Article\File as FileForm;

use \Litus\Entity\Cudi\File;
use \Litus\Entity\Cudi\Articles\Stub;
use \Litus\Entity\Cudi\Articles\StockArticles\Internal;
use \Litus\Entity\Cudi\Articles\MetaInfo;
use \Litus\Entity\Cudi\Articles\StockArticles\External;
use \Litus\Entity\Cudi\Articles\ArticleHistory;

use \Litus\FlashMessenger\FlashMessage;

use \Zend\Json\Json;
use \Zend\File\Transfer\Adapter\Http as FileUpload;

/**
 *
 * This class controlls management and adding of articles.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 */
class ArticleController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }

    public function addAction()
    {
        $form = new Add();

        $this->view->form = $form;
         
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				$metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				$supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				if ($formData['stock']) {
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
							->findOneById($formData['binding']);

						$frontColor = $this->getEntityManager()
							->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
							->findOneById($formData['front_color']);

		                $article = new Internal(
		                	$formData['title'],
	                        $metaInfo,
	                        $formData['purchaseprice'],
	                        $formData['sellprice_nomember'],
	                        $formData['sellprice_member'],
		 					$formData['barcode'],
	                        $formData['bookable'],
	                        $formData['unbookable'],
	                        $supplier,
	                        $formData['can_expire'],
							$formData['nb_black_and_white'],
	                        $formData['nb_colored'],
	                        $binding,
	                        $formData['official'],
	                        $formData['rectoverso'],
	                        $frontColor,
	                        $formData['front_text_colored']
		                );
					} else {
						$article = new External(
		                	$formData['title'],
	                        $metaInfo,
	                        $formData['purchase_price'],
	                        $formData['sellprice_nomember'],
	                        $formData['sellprice_member'],
							$formData['barcode'],
	                        $formData['bookable'],
	                        $formData['unbookable'],
	                        $supplier,
	                        $formData['can_expire']
		           		);
					}
				} else {
					$article = new Stub(
	                	$formData['title'],
                        $metaInfo
	           		);
				}
					
				$this->getEntityManager()->persist($metaInfo);
                $this->getEntityManager()->persist($article);

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully created!'
                    )
                );
                
				$this->_redirect('manage');
			}
        }
    }
    
    public function manageAction()
	{
		$this->view->inlineScript()->appendFile($this->view->baseUrl('/_admin/js/cudi.searchDatabase.js'));
		$this->view->paginator = $this->_createPaginator(
            'Litus\Entity\Cudi\Article',
            array(
                'removed' => false
            )
        );
    }

	public function editAction()
	{
		$article = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Article')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $article)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$form = new Edit();
		$form->populate($article);

        $this->view->form = $form;
        $this->view->article = $article;

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				$article->getMetaInfo()->setAuthors($formData['author'])
					->setPublishers($formData['publisher'])
					->setYearPublished($formData['year_published']);
				
				$article->setTitle($formData['title']);
				
				if ($formData['stock']) {
					$supplier = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Supplier')
						->findOneById($formData['supplier']);
						
					$article->setPurchasePrice($formData['purchase_price'])
						->setSellPrice($formData['sellprice_nomember'])
						->setSellPriceMembers($formData['sellprice_member'])
						->setBarcode($formData['barcode'])
						->setIsBookable($formData['bookable'])
						->setIsUnbookable($formData['unbookable'])
						->setSupplier($supplier)
						->setCanExpire($formData['can_expire']);
				}
				
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
						->findOneById($formData['binding']);

					$frontColor = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
						->findOneById($formData['front_color']);
						
					$article->setNbBlackAndWhite($formData['nb_black_and_white'])
						->setNbColored($formData['nb_colored'])
						->setBinding($binding)
						->setIsOfficial($formData['official'])
						->setIsRectoVerso($formData['rectoverso'])
						->setFrontColor($frontColor)
						->setFrontPageTextColored($formData['front_text_colored']);
				}

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully updated!'
                    )
                );

                $this->_redirect('manage');
			}
        }
	}

    public function deleteAction()
	{
		$article = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Article')
            ->findOneById($this->getRequest()->getParam('id'));

		if (null == $article)
			throw new Zend\Controller\Action\Exception("Page not found", 404);

		$this->view->article = $article;

		if (null !== $this->getRequest()->getParam('confirm')) {
            if (1 == $this->getRequest()->getParam('confirm')) {
				$article->setRemoved(true);

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully removed!'
                    )
                );
            }

            $this->_redirect('manage');
        }
	}

	public function searchAction()
	{
		$this->broker('contextSwitch')
            ->addActionContext('search', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();
        
        $this->broker('layout')->disableLayout();

        $json = new Json();

		$this->_initAjax();
		
		switch($this->getRequest()->getParam('field')) {
			case 'title':
				$articles = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Article')
					->findAllByTitle($this->getRequest()->getParam('string'));
				break;
			case 'author':
				$articles = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Article')
					->findAllByAuthor($this->getRequest()->getParam('string'));
				break;
			case 'publisher':
				$articles = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Article')
					->findAllByPublisher($this->getRequest()->getParam('string'));
				break;
		}
		$result = array();
		foreach($articles as $article) {
			$item = (object) array();
			$item->id = $article->getId();
			$item->title = $article->getTitle();
			$item->author = $article->getMetaInfo()->getAuthors();
			$item->publisher = $article->getMetaInfo()->getPublishers();
			$item->yearPublished = $article->getMetaInfo()->getYearPublished();
			$item->isStock = $article->isStock();
			$item->versionNumber = $article->getVersionNumber();
			$result[] = $item;
		}
		echo $json->encode($result);
	}
	
	public function managefilesAction()
	{
		$this->view->inlineScript()->appendFile($this->view->baseUrl('/_admin/js/downloadFile.js'));

		$article = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Article')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $article)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$form = new FileForm();
		
		$this->view->form = $form;
        $this->view->article = $article;
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
        	
        	if ($form->isValid($formData)) {
        		$upload = new FileUpload();
        		$originalName = $upload->getFileName(null, false);
        		
        		$fileName = '';
        		do{
        		    $fileName = '/' . sha1(uniqid());
        		} while (file_exists('../resources/files/cudi/' . $fileName));
        		
        		$upload->addFilter('Rename', '../resources/files/cudi/' . $fileName);
        		$upload->receive();
        		
        		$file = new File($fileName, $originalName, $formData['description'], $article);
        		$this->getEntityManager()->persist($file);
        		
        		$this->broker('flashmessenger')->addMessage(
        		    new FlashMessage(
        		        FlashMessage::SUCCESS,
        		        'SUCCESS',
        		        'The file was successfully added!'
        		    )
        		);
        		$this->_redirect('managefiles', null, null, array('id' => $article->getId()));
        	}
        }
	}
	
	public function deletefileAction()
	{
		$file = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\File')
            ->findOneById($this->getRequest()->getParam('id'));

		if (null == $file)
			throw new Zend\Controller\Action\Exception("Page not found", 404);
		
		$this->view->articleFile = $file;

		if (null !== $this->getRequest()->getParam('confirm')) {
            if (1 == $this->getRequest()->getParam('confirm')) {
            	unlink('../resources/files/cudi/' . $file->getPath());
            	$this->getEntityManager()->remove($file);

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The file was successfully removed!'
                    )
                );
            }

            $this->_redirect('managefiles', null, null, array('id' => $file->getInternalArticle()->getId()));
        }
	}
	
	public function downloadfileAction()
	{
		$this->broker('layout')->disableLayout(); 
		$this->broker('viewRenderer')->setNoRender();
		
		$file = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\File')
            ->findOneById($this->getRequest()->getParam('id'));

		if (null == $file)
			throw new Zend\Controller\Action\Exception("Page not found", 404);
		
		// TODO: move this to init function
		$this->getResponse()->setHeader(
			'Content-Disposition', 'inline; filename="' . $file->getName() . '"'
		)->setHeader(
			'Content-type', 'application/octet-stream'
		);
		
		readfile('../resources/files/cudi/' . $file->getPath());
	}
	
	public function newversionAction()
	{
		$article = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Article')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $article)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$form = new NewVersion();
		$form->populate($article);

        $this->view->form = $form;
        $this->view->article = $article;
         
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				$metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				$supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				if ($formData['stock']) {
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
							->findOneById($formData['binding']);

						$frontColor = $this->getEntityManager()
							->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
							->findOneById($formData['front_color']);

		                $newVersion = new Internal(
		                	$formData['title'],
	                        $metaInfo,
	                        $formData['purchaseprice'],
	                        $formData['sellprice_nomember'],
	                        $formData['sellprice_member'],
		 					$formData['barcode'],
	                        $formData['bookable'],
	                        $formData['unbookable'],
	                        $supplier,
	                        $formData['can_expire'],
							$formData['nb_black_and_white'],
	                        $formData['nb_colored'],
	                        $binding,
	                        $formData['official'],
	                        $formData['rectoverso'],
	                        $frontColor,
	                        $formData['front_text_colored']
		                );
		                
		                foreach($article->getFiles() as $file) {
		                	$fileName = '';
		                	do{
		                	    $fileName = '/' . sha1(uniqid());
		                	} while (file_exists('../resources/files/cudi/' . $fileName));
		                	copy('../resources/files/cudi/' . $file->getPath(), '../resources/files/cudi/' . $fileName);
		                	$newFile = new File($fileName, $file->getName(), $file->getDescription(), $newVersion);
		                	$this->getEntityManager()->persist($newFile);
		                }
					} else {
						$newVersion = new External(
		                	$formData['title'],
	                        $metaInfo,
	                        $formData['purchase_price'],
	                        $formData['sellprice_nomember'],
	                        $formData['sellprice_member'],
							$formData['barcode'],
	                        $formData['bookable'],
	                        $formData['unbookable'],
	                        $supplier,
	                        $formData['can_expire']
		           		);
					}
				} else {
					$newVersion = new Stub(
	                	$formData['title'],
                        $metaInfo
	           		);
				}
				
				$history = new ArticleHistory($newVersion, $article);
					
				$this->getEntityManager()->persist($metaInfo);
                $this->getEntityManager()->persist($newVersion);
                $this->getEntityManager()->persist($history);

                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The new version of the article was successfully created!'
                    )
                );
                
				$this->_redirect('manage');
			}
        }
    }
}