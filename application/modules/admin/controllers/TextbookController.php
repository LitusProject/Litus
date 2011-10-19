<?php

namespace Admin;

use Doctrine\ORM\EntityManager;

use \Admin\Form\Textbook\Add;
use \Admin\Form\Textbook\AddInternal;

use \Litus\Entity\Cudi\Articles\StockArticles\Internal;
use \Litus\Entity\Cudi\Articles\MetaInfo;
use \Litus\Entity\Cudi\Articles\StockArticles\External;

/**
 *
 * This class controlls management and adding of textbooks.
 * @author Niels Avonds
 *
 */
class TextbookController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('add');
    }

    public function addAction()
    {
        $form = new Add();
        $internal_form = $form->getInternalForm();

        $this->view->form = $form;
        $this->view->textbookCreated = false;
         
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            /*
             * If this isn't an internal article, remove all the validators and required flags from that subform.
            *
            * Remember them in arrays to restore it later.
            */
            $validators = array();
            $required = array();
            if (!$formData['internal']) {
                foreach ($internal_form->getElements() as $formelement) {
                    $validators[$formelement->getName()] = $formelement->getValidators();
                    $required[$formelement->getName()] = $formelement->isRequired();
                    $formelement->clearValidators();
                    $formelement->setRequired(false);
                }
            }

            if($form->isValid($formData)) {
                $metaInfo = new MetaInfo($formData['author'], $formData['publisher'], $formData['year_published']);
                 
                $title = $formData['title'];
                $purchase_price = $formData['purchaseprice'];
                $sellPrice = $formData['sellpricenomember'];
                $sellPriceMembers = $formData['sellpricemember'];
                $barcode = $formData['barcode'];
                $bookable = $formData['bookable'];
                $unbookable = $formData['unbookable'];

                $supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
                $canExpire = $formData['canExpire'];
                				
                if (!$formData['internal']) {
                    $article = new External(
                        $title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode,
                        $bookable, $unbookable, $supplier, $canExpire
                    );

                } else {
					$binding = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
						->findOneById($formData['binding']);
						
					$frontColor = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
						->findOneById($formData['frontcolor']);
		
                   	$article = new Internal(
                        $title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode,
                        $bookable, $unbookable, $supplier, $canExpire, $formData['nbBlackAndWhite'],
						$formData['nbColored'], $binding, $formData['official'], $formData['rectoverso'], $frontColor
                    );
                }
                 
                $this->getEntityManager()->persist($metaInfo);
                $this->getEntityManager()->persist($article);
                $this->view->textbookCreated = true;
                 
            }
            	
            // Make sure the validators and required flags are added again.
            if (!$formData['internal']) {
                foreach ($internal_form->getElements() as $formelement) {
                    if (array_key_exists ($formelement->getName(), $validators))
                        $formelement->setValidators($validators[$formelement->getName()]);
                    if (array_key_exists ($formelement->getName(), $required))
                        $formelement->setRequired($required[$formelement->getName()]);
                }
            }

        }
    }
    
    public function manageAction()
	{
        $em = $this->getEntityManager();
        $this->view->articles = $em->getRepository('Litus\Entity\Cudi\Article')->findAll();
    }
}