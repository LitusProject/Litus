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

use CommonBundle\Component\Util\TmpFile,
	CommonBundle\Component\Util\Xml\XmlGenerator,
	CommonBundle\Component\Util\Xml\XmlObject,
	CommonBundle\Entity\Stock\Order,
	CommonBundle\Entity\Stock\OrderItem,
	Doctrine\ORM\EntityManager;

class OrderXmlGenerator
{

	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;
	
	/**
	 * @var \CudiBundle\Entity\Stock\Order
	 */
	private $_order;
	
	/**
	 * Create a Order XML Generator.
	 *
	 * @param \Doctrine\ORM\EntityManager $entityManager The entityManager
	 * @param \CudiBundle\Entity\Stock\Order $order The order
	 */
    public function __construct(EntityManager $entityManager, Order $order)
    {
    	$this->_order = $order;
    	$this->_entityManager = $entityManager;
    }
	
	/**
	 * Generate an archive to download.
	 *
	 * @param \CommonBundle\Component\Util\TmpFile $archive The file to write to
	 */
	public function generateArchive(TmpFile $archive)
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
	
    private function _generateXml(OrderItem $item, TmpFile $tmpFile)
    {
    	$configs = $this->_entityManager
    		->getRepository('CommonBundle\Entity\General\Config');
        
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
