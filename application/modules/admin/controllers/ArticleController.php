<?php

namespace Admin;

use Doctrine\ORM\EntityManager;

use \Admin\Form\Article\Add;
use \Admin\Form\Article\Edit;

use \Litus\Entity\Cudi\Articles\StockArticles\Internal;
use \Litus\Entity\Cudi\Articles\MetaInfo;
use \Litus\Entity\Cudi\Articles\StockArticles\External;
use \Litus\FlashMessenger\FlashMessage;

/**
 *
 * This class controlls management and adding of articles.
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
			
			if (!$formData['internal']) {
				$validators = array();
				$required = array();
                
				foreach ($form->getDisplayGroup('internal_form')->getElements() as $formElement) {
					$validators[$formElement->getName()] = $formElement->getValidators();
					$required[$formElement->getName()] = $formElement->isRequired();
					$formElement->clearValidators();
					$formElement->setRequired(false);
				}
			}
			
			if ($form->isValid($formData)) {
				$metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				$supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
						->findOneById($formData['binding']);

					$frontColor = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
						->findOneById($formData['frontcolor']);

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
                        $frontColor
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
			
			if (!$formData['internal']) {
				foreach ($form->getDisplayGroup('internal_form')->getElements() as $formElement) {
					if (array_key_exists ($formElement->getName(), $validators))
			 			$formElement->setValidators($validators[$formElement->getName()]);
					if (array_key_exists ($formElement->getName(), $required))
						$formElement->setRequired($required[$formElement->getName()]);
				}
			}
        }
    }
    
    public function manageAction()
	{
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

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
			
			if (!$formData['internal']) {
				$validators = array();
				$required = array();

				foreach ($form->getDisplayGroup('internal_form')->getElements() as $formElement) {
					$validators[$formElement->getName()] = $formElement->getValidators();
					$required[$formElement->getName()] = $formElement->isRequired();
					$formElement->clearValidators();
					$formElement->setRequired(false);
				}
			}
			
			if ($form->isValid($formData)) {
				$article->getMetaInfo()->setAuthors($formData['author'])
					->setPublishers($formData['publisher'])
					->setYearPublished($formData['year_published']);
				
				$supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				$article->setTitle($formData['title'])
					->setPurchasePrice($formData['purchaseprice'])
					->setSellPrice($formData['sellpricenomember'])
					->setSellPriceMembers($formData['sellpricemember'])
					->setBarcode($formData['barcode'])
					->setIsBookable($formData['bookable'])
					->setIsUnbookable($formData['unbookable'])
					->setSupplier($supplier)
					->setCanExpire($formData['canExpire']);
				
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
						->findOneById($formData['binding']);

					$frontColor = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
						->findOneById($formData['frontcolor']);
						
					$article->setNbBlackAndWhite($formData['nbBlackAndWhite'])
						->setNbColored($formData['nbColored'])
						->setBinding($binding)
						->setIsOfficial($formData['official'])
						->setIsRectoVerso($formData['rectoverso'])
						->setFrontColor($frontColor);
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
			
			if (!$formData['internal']) {
				foreach ($form->getDisplayGroup('internal_form')->getElements() as $formElement) {
					if (array_key_exists ($formElement->getName(), $validators))
			 			$formElement->setValidators($validators[$formElement->getName()]);
					if (array_key_exists ($formElement->getName(), $required))
						$formElement->setRequired($required[$formElement->getName()]);
				}
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
}