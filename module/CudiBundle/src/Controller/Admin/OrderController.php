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
	CommonBundle\Component\Util\File as FileUtil,
	CommonBundle\Component\Util\File\TmpFile,
	CommonBundle\Component\Util\Xml\XmlGenerator,
	CommonBundle\Component\Util\Xml\XmlObject,
	CudiBundle\Component\Generator\OrderPdfGenerator,
	CudiBundle\Component\Generator\OrderXmlGenerator,
	CudiBundle\Entity\Stock\Order,
	CudiBundle\Entity\Stock\OrderItem,
	CudiBundle\Form\Admin\Order\Add as AddForm,
	CudiBundle\Form\Admin\Order\AddItem as AddItemForm,
	CudiBundle\Form\Admin\Order\Edit as EditForm,
	Zend\Http\Headers,
	Zend\Pdf\Page as PdfPage,
	Zend\Pdf\PdfDocument;

/**
 * OrderController
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OrderController extends \CommonBundle\Component\Controller\ActionController
{

    public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
		    'CudiBundle\Entity\Supplier',
		    $this->getParam('page')
		);
		
		return array(
			'paginator' => $paginator,
			'paginationControl' => $this->paginator()->createControl(true)
		);
    }

	public function supplierAction()
	{
		$supplier = $this->_getSupplier();
        
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Order',
            $this->getParam('page'),
            array(
            	'supplier' => $supplier->getId(),
            )
        );
        
        return array(
        	'supplier' => $supplier,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
        );
	}
	
	public function editAction()
	{
		$order = $this->_getOrder();

		return array(
			'order' => $order,
		);
	}
	
	public function addAction()
	{
		$form = new AddItemForm($this->getEntityManager());
				
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findOneByBarcode($formData['stockArticle']);
				
				$item = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\OrderItem')
					->addNumberByArticle($article, $formData['number']);
				
				$this->getEntityManager()->flush();	
				
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order item was successfully added!'
                    )
				);
				
				$this->redirect()->toRoute(
					'admin_order',
					array(
						'action' => 'edit',
						'id' => $item->getOrder()->getId(),
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
		$item = $this->_getOrderItem();
				
		if (null !== $this->getParam('confirm')) {
			if (1 == $this->getParam('confirm')) {
				$this->getEntityManager()->remove($item);

				$this->getEntityManager()->flush();

				$this->flashMessenger()->addMessage(
            		new FlashMessage(
                		FlashMessage::SUCCESS,
                    	'SUCCESS',
                    	'The order item was successfully removed!'
                	)
            	);
			};
            
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'edit',
					'id' => $item->getOrder()->getId(),
				)
			);
        }
        
        return array(
        	'item' => $item
        );
	}
	
	public function placeAction()
	{
		$order = $this->_getOrder();
		$order->setDate(new \DateTime());
		
		$this->getEntityManager()->flush();
		
		$this->flashMessenger()->addMessage(
		    new FlashMessage(
		        FlashMessage::SUCCESS,
		        'SUCCESS',
		        'No order is successfully placed!'
		    )
		);
			
		$this->redirect()->toRoute(
			'admin_order',
			array(
				'action' => 'edit',
				'id' => $order->getId(),
			)
		);
	}
	
	public function pdfAction()
	{
		$order = $this->_getOrder();
		$file = new TmpFile();
		$document = new OrderPdfGenerator($this->getEntityManager(), $order, $file);
		$document->generate();

		$headers = new Headers();
		$headers->addHeaders(array(
			'Content-Disposition' => 'inline; filename="order.pdf"',
			'Content-type'        => 'application/pdf',
		));
		$this->getResponse()->setHeaders($headers);
		
		return array(
			'data' => $file->getContent()
		);
	}
	
	public function exportAction()
	{
		$order = $this->_getOrder();
		
		$document = new OrderXmlGenerator($this->getEntityManager(), $order);
		
		$headers = new Headers();
		$headers->addHeaders(array(
			'Content-Disposition' => 'inline; filename="order.zip"',
			'Content-type'        => 'application/zip',
		));
		$this->getResponse()->setHeaders($headers);
		
		$archive = new TmpFile();
		$document->generateArchive($archive);
		
		$handle = fopen($archive->getFileName(), 'r');
		$data = fread($handle, filesize($archive->getFileName()));
		fclose($handle);
		
		return array(
			'data' => $data,
		);
	}
	
	private function _getSupplier()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the supplier!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $supplier = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Supplier')
	        ->findOneById($this->getParam('id'));
		
		if (null === $supplier) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No supplier with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $supplier;
	}
	
	private function _getOrder()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the order!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $order = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Stock\Order')
	        ->findOneById($this->getParam('id'));
		
		if (null === $order) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No order with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $order;
	}
	
	private function _getOrderItem()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the order item!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $item = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Stock\OrderItem')
	        ->findOneById($this->getParam('id'));
		
		if (null === $item) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No order item with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_order',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $item;
	}
}