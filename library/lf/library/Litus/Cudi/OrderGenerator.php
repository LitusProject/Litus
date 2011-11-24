<?php

namespace Litus\Cudi;

use \Litus\Entity\Cudi\Stock\Order;

use \Litus\Util\Xml\XmlGenerator;
use \Litus\Util\Xml\XmlObject;
use \Litus\Util\TmpFile;

use \Zend\Registry;

class OrderGenerator extends \Litus\Br\DocumentGenerator
{

	/**
	 * @var \Litus\Entity\Cudi\Stock\Order
	 */
	private $_order;
	
    public function __construct(Order $order)
    {
    	$file = new TmpFile();
    	
    	parent::__construct(
    	    Registry::get('litus.resourceDirectory') . '/pdf_generators/cudi/order.xsl',
    	    $file->getFilename()
    	);
    	$this->_order = $order;
    }

    protected function _generateXml(TmpFile $tmpFile)
    {
        $now = new \DateTime();
        $union_short_name = 'VTK';
        $union_name = 'VTK VZW';
        $logo = '../images/logo/logo.jpg';
        $cudi_name = 'CURSUSDIENST';
        $cudi_mail = 'cudi@vtk.be';
        $cudi_phone = '0472/ 24 97 08';
        $delivery_address = array(
        	'name' => 'VTK Cursusdienst',
        	'street' => 'Kasteelpark Arenberg 41',
        	'city' => '3001 Heverlee',
        	'extra' => '(inrit via Celestijnenlaan)'
        );
        $billing_address = array(
        	'name' => 'VTK VZW',
        	'person' => 'Ruben Dilissen',
        	'street' => 'Studentenwijk Arenberg 6/0',
        	'city' => '3001 Heverlee'
        );
        
        $external_items = array();
        $internal_items = array();
        foreach($this->_order->getOrderItems() as $item) {
        	if ($item->getArticle()->isInternal()) {
        		$internal_items[] = new XmlObject(
        			'external_item',
        			null,
        			array(
        				new XmlObject(
        					'barcode',
        					null,
        					(string) $item->getArticle()->getBarcode()
        				),
        				new XmlObject(
        					'title',
        					null,
        					$item->getArticle()->getTitle()
        				),
        				new XmlObject(
        					'recto_verso',
        					null,
        					$item->getArticle()->isRectoVerso() ? '1' : '0'
        				),
        				new XmlObject(
        					'binding',
        					null,
        					$item->getArticle()->getBinding()->getName()
        				),
        				new XmlObject(
        					'nb_pages',
        					null,
        					(string) $item->getArticle()->getNbPages()
        				),
        				new XmlObject(
        				    'number',
        				    null,
        				    (string) $item->getNumber()
        				)
        			)
        		);
        	} else {
        		$external_items[] = new XmlObject(
        			'external_item',
        			null,
        			array(
        				new XmlObject(
        					'isbn',
        					null,
        					(string) $item->getArticle()->getBarcode()
        				),
        				new XmlObject(
        					'title',
        					null,
        					$item->getArticle()->getTitle()
        				),
        				new XmlObject(
        					'author',
        					null,
        					$item->getArticle()->getMetaInfo()->getAuthors()
        				),
        				new XmlObject(
        					'publisher',
        					null,
        					$item->getArticle()->getMetaInfo()->getPublishers()
        				),
        				new XmlObject(
        				    'number',
        				    null,
        				    (string) $item->getNumber()
        				)
        			)
        		);
        	}
        }
        
        $xml = new XmlGenerator($tmpFile);
        
        $xml->append(
        	new XmlObject(
        		'order',
        		array(
        			'date' => $now->format('d F Y')
        		),
        		array(
        			new XmlObject(
        				'our_union',
        				array(
        					'short_name' => $union_short_name
        				),
        				array(
        					new XmlObject(
        						'name',
        						null,
        						$union_name
        					),
        					new XmlObject(
        						'logo',
        						null,
        						$logo
        					)
        				)
        			),
        			new XmlObject(
        				'cudi',
        				array(
        					'name' => $cudi_name
        				),
        				array(
        					 new XmlObject(
        					 	'mail',
        					 	null,
        					 	$cudi_mail
        					 ),
        					 new XmlObject(
        					 	'phone',
        					 	null,
        					 	$cudi_phone
        					 ),
        					 new XmlObject(
        					 	'delivery_address',
        					 	null,
        					 	array(
        					 		new XmlObject(
        					 			'name',
        					 			null,
        					 			$delivery_address['name']
        					 		),
        					 		new XmlObject(
        					 			'street',
        					 			null,
        					 			$delivery_address['street']
        					 		),
        					 		new XmlObject(
        					 			'city',
        					 			null,
        					 			$delivery_address['city']
        					 		),
        					 		new XmlObject(
        					 			'extra',
        					 			null,
        					 			$delivery_address['extra']
        					 		)
        					 	)
        					 ),
        					 new XmlObject(
        					 	'billing_address',
        					 	null,
        					 	array(
        					 		new XmlObject(
        					 			'name',
        					 			null,
        					 			$billing_address['name']
        					 		),
        					 		new XmlObject(
        					 			'person',
        					 			null,
        					 			$billing_address['person']
        					 		),
        					 		new XmlObject(
        					 			'street',
        					 			null,
        					 			$billing_address['street']
        					 		),
        					 		new XmlObject(
        					 			'city',
        					 			null,
        					 			$billing_address['city']
        					 		)
        					 	)
        					 )
        				)
        			),
        			new XmlObject(
        				'external_items',
        				null,
        				$external_items
        			),
        			new XmlObject(
        				'internal_items',
        				null,
        				$internal_items
        			)
        		)
        	)
        );
    }
}
