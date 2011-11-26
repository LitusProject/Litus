<?php

namespace Litus\Cudi;

use \Litus\Entity\Cudi\Stock\Order;

use \Litus\Application\Resource\Doctrine as DoctrineResource;
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
    	$configs = self::_getConfigRepository();
    	
        $now = new \DateTime();
        $union_short_name = $configs->getConfigValue('cudi.union_short_name');
        $union_name = $configs->getConfigValue('cudi.union_name');
        $logo = $configs->getConfigValue('cudi.union_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = Registry::get(DoctrineResource::REGISTRY_KEY)
        	->getRepository('Litus\Entity\Users\Person')
        	->findOneById($configs->getConfigValue('cudi.person'));

        $delivery_address = array(
        	'name' => $configs->getConfigValue('cudi.delivery_address_name'),
        	'street' => $configs->getConfigValue('cudi.delivery_address_street'),
        	'city' => $configs->getConfigValue('cudi.delivery_address_city'),
        	'extra' => $configs->getConfigValue('cudi.delivery_address_extra'),
        );
        $billing_address = array(
        	'name' => $configs->getConfigValue('cudi.billing_address_name'),
        	'street' => $configs->getConfigValue('cudi.billing_address_street'),
        	'city' => $configs->getConfigValue('cudi.billing_address_city'),
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
        					 	$person->getTelephone()
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
        					 			$person->getFullname()
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
