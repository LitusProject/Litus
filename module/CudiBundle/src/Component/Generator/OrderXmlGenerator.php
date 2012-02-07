<?php

namespace CudiBundle\Component\Generator;

use CommonBundle\Entity\Stock\Order,

	CommonBundle\Component\Util\Xml\XmlGenerator,
	CommonBundle\Component\Util\Xml\XmlObject,
	CommonBundle\Component\Util\TmpFile,
	
	Doctrine\ORM\EntityManager;

class OrderXmlGenerator
{

	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;
	
	/**
	 * @var \Litus\Entity\Cudi\Stock\Order
	 */
	private $_order;
	
    public function __construct(EntityManager $entityManager, Order $order)
    {
    	$this->_order = $order;
    	$this->_entityManager = $entityManager;
    }

	public function generateArchive($archive)
	{
		$zip = new \ZipArchive();
		
		foreach($this->_order->getOrderItems() as $item) {
			if (!$item->getArticle()->isInternal())
				continue;
			
			$zip->open($archive->getFileName(), \ZIPARCHIVE::CREATE);
			$xmlFile = new TmpFile();
			$this->_generateXml($item, $xmlFile);
			
			$zip->addFile($xmlFile->getFilename(), $item->getId() . '.xml');
			foreach($item->getArticle()->getFiles() as $file)
				$zip->addFile('../resources/files/cudi/' . $file->getPath(), $file->getName());
			
			$zip->close();
		}
	}

    private function _generateXml($item, $tmpFile)
    {
    	$configs = $this->_entityManager
    		->getRepository('Litus\Entity\General\Config');
        
        $xml = new XmlGenerator($tmpFile);
		
		$attachments = array();
		$num = 1;
		foreach($item->getArticle()->getFiles() as $file) {
			$attachments[] = new XmlObject(
				'Attachment',
				array(
					'AttachmentKey' => 'File' . $num++,
					'FileName' => $file->getName()
				),
				null
			);
		}
		
		$itemValues = array(
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'titel'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						$item->getArticle()->getTitle()
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'aantal'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) $item->getNumber()
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'barcode'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) $item->getArticle()->getBarcode()
					)
				)
			),
			// TODO: generate text
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'afwerking'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) ''
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'kleur'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						$item->getArticle()->getNbColored() > 0 ? 'kleur' : 'zwart/wit'
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'zijde'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) $item->getArticle()->isRectoVerso() ? 'Recto-Verso' : 'Recto'
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'TypeDrukOpdracht'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						'Cursus'
					)
				)
			),
			// TODO: generate text
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'DatumOpdrachtKlaar'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) ''
					)
				)
			),
			// TODO: generate text
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'Referentie'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						(string) ''
					)
				)
			),
			new XmlObject(
				'ItemValue',
				array(
					'ItemKey' => 'Opmerking'
				),
				array(
					new XmlObject(
						'LastUsedValue',
						null,
						''
					)
				)
			)
		);

        $xml->append(
        	new XmlObject(
        		'Document',
        		null,
        		array(
        			new XmlObject(
        				'Job',
        				array(
        					'JobID' => 'vtk-' . $this->_order->getDate()->format('YmdHi') . '-'
        				),
        				array(
	        				new XmlObject(
	        					'Attachments',
	        					null,
	        					$attachments
	        				),
	        				new XmlObject(
	        					'ItemValues',
	        					null,
	        					$itemValues
	        				)
	        			)
        			)
        		)
        	)
        );
    }
}
