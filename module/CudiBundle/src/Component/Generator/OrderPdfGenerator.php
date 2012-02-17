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
 
namespace CudiBundle\Component\Generator;

use CommonBundle\Component\Util\File\TmpFile,
	CommonBundle\Component\Util\Xml\Generator,
	CommonBundle\Component\Util\Xml\Object,
	CudiBundle\Entity\Stock\Order,
	Doctrine\ORM\EntityManager;

class OrderPdfGenerator extends \CommonBundle\Component\Generator\DocumentGenerator
{
	
	/**
	 * @var \CudiBundle\Entity\Stock\Order
	 */
	private $_order;
	
	/**
	 * Create a new Order PDF Generator.
	 *
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \CudiBundle\Entity\Stock\Order $order The order
	 * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
	 */
    public function __construct(EntityManager $entityManager, Order $order, TmpFile $file)
    {
    	$filePath = $entityManager
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.pdf_generator_path');
    				
	   	parent::__construct(
	   		$entityManager,
    	    $filePath . '/orders/order.xsl',
    	    $file->getFilename()
    	);
    	$this->_order = $order;
    }
	
	/**
	 * Generate the XML for the fop.
	 *
	 * @param \CommonBundle\Component\Util\TmpFile $tmpFile The file to write to.
	 */
    protected function _generateXml(TmpFile $tmpFile)
    {
    	$configs = $this->_getConfigRepository();
    	
        $now = new \DateTime();
        $union_short_name = $configs->getConfigValue('cudi.union_short_name');
        $union_name = $configs->getConfigValue('cudi.union_name');
        $logo = $configs->getConfigValue('cudi.union_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
        	->getRepository('CommonBundle\Entity\Users\Person')
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
        		$internal_items[] = new Object(
        			'internal_item',
        			null,
        			array(
        				new Object(
        					'barcode',
        					null,
        					(string) $item->getArticle()->getBarcode()
        				),
        				new Object(
        					'title',
        					null,
        					$item->getArticle()->getTitle()
        				),
        				new Object(
        					'recto_verso',
        					null,
        					$item->getArticle()->isRectoVerso() ? '1' : '0'
        				),
        				new Object(
        					'binding',
        					null,
        					$item->getArticle()->getBinding()->getName()
        				),
        				new Object(
        					'nb_pages',
        					null,
        					(string) $item->getArticle()->getNbPages()
        				),
        				new Object(
        				    'number',
        				    null,
        				    (string) $item->getNumber()
        				)
        			)
        		);
        	} else {
        		$external_items[] = new Object(
        			'external_item',
        			null,
        			array(
        				new Object(
        					'isbn',
        					null,
        					(string) $item->getArticle()->getBarcode()
        				),
        				new Object(
        					'title',
        					null,
        					$item->getArticle()->getTitle()
        				),
        				new Object(
        					'author',
        					null,
        					$item->getArticle()->getMetaInfo()->getAuthors()
        				),
        				new Object(
        					'publisher',
        					null,
        					$item->getArticle()->getMetaInfo()->getPublishers()
        				),
        				new Object(
        				    'number',
        				    null,
        				    (string) $item->getNumber()
        				)
        			)
        		);
        	}
        }
        
        $xml = new Generator($tmpFile);

        $xml->append(
        	new Object(
        		'order',
        		array(
        			'date' => $now->format('d F Y')
        		),
        		array(
        			new Object(
        				'our_union',
        				array(
        					'short_name' => $union_short_name
        				),
        				array(
        					new Object(
        						'name',
        						null,
        						$union_name
        					),
        					new Object(
        						'logo',
        						null,
        						$logo
        					)
        				)
        			),
        			new Object(
        				'cudi',
        				array(
        					'name' => $cudi_name
        				),
        				array(
        					 new Object(
        					 	'mail',
        					 	null,
        					 	$cudi_mail
        					 ),
        					 new Object(
        					 	'phone',
        					 	null,
        					 	$person->getPhoneNumber()
        					 ),
        					 new Object(
        					 	'delivery_address',
        					 	null,
        					 	array(
        					 		new Object(
        					 			'name',
        					 			null,
        					 			$delivery_address['name']
        					 		),
        					 		new Object(
        					 			'street',
        					 			null,
        					 			$delivery_address['street']
        					 		),
        					 		new Object(
        					 			'city',
        					 			null,
        					 			$delivery_address['city']
        					 		),
        					 		new Object(
        					 			'extra',
        					 			null,
        					 			$delivery_address['extra']
        					 		)
        					 	)
        					 ),
        					 new Object(
        					 	'billing_address',
        					 	null,
        					 	array(
        					 		new Object(
        					 			'name',
        					 			null,
        					 			$billing_address['name']
        					 		),
        					 		new Object(
        					 			'person',
        					 			null,
        					 			$person->getFullname()
        					 		),
        					 		new Object(
        					 			'street',
        					 			null,
        					 			$billing_address['street']
        					 		),
        					 		new Object(
        					 			'city',
        					 			null,
        					 			$billing_address['city']
        					 		)
        					 	)
        					 )
        				)
        			),
        			new Object(
        				'external_items',
        				null,
        				$external_items
        			),
        			new Object(
        				'internal_items',
        				null,
        				$internal_items
        			)
        		)
        	)
        );
    }
}
