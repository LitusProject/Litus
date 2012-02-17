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
	CudiBundle\Entity\Articles\ArticleHistory,
	CudiBundle\Entity\Articles\MetaInfo,
	CudiBundle\Entity\Articles\Stub,
	CudiBundle\Entity\Articles\StockArticles\External,
	CudiBundle\Entity\Articles\StockArticles\Internal,
	CudiBundle\Entity\File,
	CudiBundle\Form\Admin\Article\Add as AddForm,
	CudiBundle\Form\Admin\Article\Edit as EditForm,
	CudiBundle\Form\Admin\Article\File as FileForm,
	CudiBundle\Form\Admin\Article\NewVersion as NewVersionForm,
	Doctrine\ORM\EntityManager,
	Zend\File\Transfer\Adapter\Http as FileUpload,
	Zend\Http\Headers,
	Zend\Json\Json;

/**
 *
 * This class controlls management and adding of articles.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 */
class ArticleController extends \CommonBundle\Component\Controller\ActionController
{

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
         
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if ($form->isValid($formData)) {
				$metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				$supplier = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Supplier')
					->findOneById($formData['supplier']);
				
				if ($formData['stock']) {
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
							->findOneById($formData['binding']);

						$frontColor = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\StockArticles\Color')
							->findOneById($formData['front_color']);

		                $article = new Internal(
							$this->getEntityManager(),
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
							$this->getEntityManager(),
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
				
				$this->getEntityManager()->flush();
				
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully created!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'admin_article',
                	array(
                		'action' => 'manage'
                	)
                );
			}
        }
        
        return array(
        	'form' => $form
        );
    }
    
    public function manageAction()
	{
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Article',
            $this->getParam('page'),
            array(
                'removed' => false
            )
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }

	public function editAction()
	{
		$article = $this->_getArticle();
		
		$form = new EditForm($this->getEntityManager(), $article);

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if ($form->isValid($formData)) {
				$article->getMetaInfo()->setAuthors($formData['author'])
					->setPublishers($formData['publisher'])
					->setYearPublished($formData['year_published']);
				
				$article->setTitle($formData['title']);
				
				if ($formData['stock']) {
					$supplier = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Supplier')
						->findOneById($formData['supplier']);
						
					$article->setIsBookable($formData['bookable'])
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
						
					$article->setNbBlackAndWhite($formData['nb_black_and_white'])
						->setNbColored($formData['nb_colored'])
						->setBinding($binding)
						->setIsOfficial($formData['official'])
						->setIsRectoVerso($formData['rectoverso'])
						->setFrontColor($frontColor)
						->setFrontPageTextColored($formData['front_text_colored']);
				}
				
				$this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_article',
                	array(
                		'action' => 'manage'
                	)
                );
			}
        }
        
        return array(
        	'form' => $form,
        	'article' => $article,
        );
	}

    public function deleteAction()
	{
		$article = $this->_getArticle();

		if (null !== $this->getParam('confirm')) {
            if (1 == $this->getParam('confirm')) {
				$article->setRemoved(true);
				$this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully removed!'
                    )
                );
            }

            $this->redirect()->toRoute(
            	'admin_article',
            	array(
            		'action' => 'manage'
            	)
            );
        }
        
        return array(
        	'article' => $article,
        );
	}

	public function searchAction()
	{
		$this->initAjax();
		
		switch($this->getParam('field')) {
			case 'title':
				$articles = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Article')
					->findAllByTitle($this->getParam('string'));
				break;
			case 'author':
				$articles = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Article')
					->findAllByAuthor($this->getParam('string'));
				break;
			case 'publisher':
				$articles = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Article')
					->findAllByPublisher($this->getParam('string'));
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
		
		return array(
			'result' => $result,
		);
	}
	
	public function managefilesAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		$article = $this->_getArticle();
		
		$form = new FileForm();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        		$upload = new FileUpload();
        		$originalName = $upload->getFileName(null, false);

        		$fileName = '';
        		do{
        		    $fileName = '/' . sha1(uniqid());
        		} while (file_exists($filePath . $fileName));
        		
        		$upload->addFilter('Rename', $filePath . $fileName);
        		$upload->receive();
        		
        		$file = new File($fileName, $originalName, $formData['description'], $article);
        		$this->getEntityManager()->persist($file);
        		$this->getEntityManager()->flush();
        		
        		$this->flashMessenger()->addMessage(
        		    new FlashMessage(
        		        FlashMessage::SUCCESS,
        		        'SUCCESS',
        		        'The file was successfully added!'
        		    )
        		);
        		
        		$this->redirect()->toRoute(
        			'admin_article',
        			array(
        				'action' => 'managefiles',
        				'id' => $file->getInternalArticle()->getId()
        			)
        		);
        	}
        }
        
        return array(
        	'form' => $form,
        	'article' => $article,
        	'articleFiles' => $article->getFiles($this->getEntityManager()),
        );
	}
	
	public function deletefileAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		$file = $this->_getFile();
		
		if (null !== $this->getParam('confirm')) {
            if (1 == $this->getParam('confirm')) {
            	unlink($filePath . $file->getPath());
            	$this->getEntityManager()->remove($file);
            	$this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The file was successfully removed!'
                    )
                );
            }
            
            $this->redirect()->toRoute(
            	'admin_article',
            	array(
            		'action' => 'managefiles',
            		'id' => $file->getInternalArticle()->getId()
            	)
            );
        }
        
        return array(
        	'articleFile' => $file,
        );
	}
	
	public function downloadfileAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		$file = $this->_getFile();
		
		$headers = new Headers();
		$headers->addHeaders(array(
			'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
			'Content-type' => 'application/octet-stream',
			'Content-Length' => filesize($filePath . $file->getPath()),
		));
		$this->getResponse()->setHeaders($headers);

		$handle = fopen($filePath . $file->getPath(), 'r');
		$data = fread($handle, filesize($filePath . $file->getPath()));
		fclose($handle);
		
		return array(
			'data' => $data
		);
	}
	
	public function newversionAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		$article = $this->_getArticle();
		
		$form = new NewVersionForm($this->getEntityManager(), $article);
         
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if ($form->isValid($formData)) {
				$metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				if ($formData['stock']) {
					$supplier = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Supplier')
						->findOneById($formData['supplier']);
						
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
							->findOneById($formData['binding']);

						$frontColor = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\StockArticles\Color')
							->findOneById($formData['front_color']);

		                $newVersion = new Internal(
		                	$this->getEntityManager(),
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
		                	do {
		                	    $fileName = '/' . sha1(uniqid());
		                	} while (file_exists($filePath . $fileName));
		                	
		                	copy($filePath . $file->getPath(), $filePath . $fileName);
		                	$newFile = new File($fileName, $file->getName(), $file->getDescription(), $newVersion);
		                	$this->getEntityManager()->persist($newFile);
		                }
					} else {
						$newVersion = new External(
		                	$this->getEntityManager(),
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
				
				$history = new ArticleHistory($this->getEntityManager(), $newVersion, $article);
					
				$this->getEntityManager()->persist($metaInfo);
                $this->getEntityManager()->persist($newVersion);
                $this->getEntityManager()->persist($history);
                
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The new version of the article was successfully created!'
                    )
                );
                
				$this->redirect()->toRoute(
					'admin_article',
					array(
						'action' => 'manage'
					)
				);
			}
        }
        
        return array(
        	'form' => $form,
        	'article' => $article,
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
    
    private function _getFile()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the file!'
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
            ->getRepository('CudiBundle\Entity\File')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No file with the given id was found!'
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
}